<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Zxing\QrReader;

class DeliveryAgentDashboard extends Component
{
    use WithFileUploads;

    public $orders = [];
    public $statusFilter = '';
    public $search = '';
    public $barcode;
    public $image;
    public $statuses = [];

    public function mount()
    {
        $this->statuses = Status::whereNull('deleted_at')->pluck('name', 'id')->toArray();
        $this->loadOrders();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['statusFilter', 'search'])) {
            $this->loadOrders();
        } elseif ($propertyName === 'barcode' && $this->barcode) {
            $this->findOrderByBarcode();
        }
    }

    private function loadOrders()
    {
        $user = Auth::user();
        if (!$user->hasRole('delivery_agent')) {
            abort(403, 'Unauthorized');
        }

        $query = Order::query()
            ->where('delivery_agent_id', $user->id)
            ->with(['customer', 'status']);

        if ($this->statusFilter) {
            $query->where('status_id', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('otp', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        $this->orders = $query->get();

        Log::debug('Delivery Agent Orders', [
            'user_id' => $user->id,
            'status_filter' => $this->statusFilter,
            'search' => $this->search,
            'orders_count' => $this->orders->count(),
        ]);
    }

    public function findOrderByBarcode()
    {
        $this->validate([
            'barcode' => 'nullable|string',
        ]);

        $order = Order::whereHas('items', fn($query) => $query->where('barcode', $this->barcode))
            ->where('delivery_agent_id', Auth::id())
            ->with('items')
            ->first();

        if ($order) {
            $this->search = $order->otp;
            $this->loadOrders();
        } else {
            $this->addError('barcode', 'Order not found');
        }

        $this->barcode = null;
    }

    public function findOrderByImage()
    {
        $this->validate([
            'image' => 'nullable|image|max:2048',
        ]);

        if ($this->image) {
            $path = $this->image->store('order_images', 'public');
            try {
                $fullPath = storage_path('app/public/' . $path);
                $reader = new QrReader($fullPath);
                $barcode = $reader->text();

                if (!$barcode) {
                    throw new \Exception('No barcode detected');
                }

                $order = Order::whereHas('items', fn($query) => $query->where('barcode', $barcode))
                    ->where('delivery_agent_id', Auth::id())
                    ->with('items')
                    ->first();

                if ($order) {
                    $this->search = $order->otp;
                    $this->loadOrders();
                } else {
                    $this->addError('image', 'Order not found');
                }
            } catch (\Exception $e) {
                Log::error('Barcode decode error: ' . $e->getMessage());
                $this->addError('image', 'Barcode decode error');
            }
        }
    }

    public function updateOrderStatus($orderId, $statusId)
    {
        Validator::make(
            ['status_id' => $statusId],
            ['status_id' => 'required|exists:statuses,id'],
            [],
            ['status_id' => 'status']
        )->validate();

        $order = Order::where('id', $orderId)
            ->where('delivery_agent_id', Auth::id())
            ->firstOrFail();

        $order->update(['status_id' => $statusId]);
        $order->items()->update(['status_id' => $statusId]);

        session()->flash('message', 'Status updated successfully');
        $this->loadOrders();
    }

    public function render()
    {
        return view('livewire.delivery-agent-dashboard')->layout('layouts.app');
    }
}
