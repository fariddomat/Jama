<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Item;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Zxing\QrReader;

class DeliveryAgentDashboard extends Component
{
    use WithFileUploads;

    public $orders = []; // Actually items
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

        $query = Item::query()
            ->whereHas('order', fn($q) => $q->where('delivery_agent_id', $user->id))
            ->with(['order', 'order.customer', 'status']);

        if ($this->statusFilter) {
            $query->where('status_id', $this->statusFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('order', fn($q2) => $q2->where('otp', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('order.customer', fn($q2) => $q2->where('name', 'like', '%' . $this->search . '%'));
            });
        }

        $this->orders = $query->get();

        Log::debug('Delivery Agent Items', [
            'user_id' => $user->id,
            'status_filter' => $this->statusFilter,
            'search' => $this->search,
            'items_count' => $this->orders->count(),
        ]);
    }

    public function findOrderByBarcode()
    {
        $this->validate([
            'barcode' => 'nullable|string',
        ]);

        $item = Item::where('barcode', $this->barcode)
            ->whereHas('order', fn($query) => $query->where('delivery_agent_id', Auth::id()))
            ->with('order')
            ->first();

        if ($item) {
            $this->search = $item->order->otp;
            $this->loadOrders();
        } else {
            $this->addError('barcode', __('site.order_not_found'));
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

                $item = Item::where('barcode', $barcode)
                    ->whereHas('order', fn($query) => $query->where('delivery_agent_id', Auth::id()))
                    ->with('order')
                    ->first();

                if ($item) {
                    $this->search = $item->order->otp;
                    $this->loadOrders();
                } else {
                    $this->addError('image', __('site.order_not_found'));
                }
            } catch (\Exception $e) {
                Log::error('Barcode decode error: ' . $e->getMessage());
                $this->addError('image', __('site.barcode_decode_error'));
            }
        }
    }

    public function updateItemStatus($itemId, $statusId)
    {
        Validator::make(
            ['status_id' => $statusId],
            ['status_id' => 'required|exists:statuses,id'],
            [],
            ['status_id' => 'status_id']
        )->validate();

        $item = Item::where('id', $itemId)
            ->whereHas('order', fn($q) => $q->where('delivery_agent_id', Auth::id()))
            ->firstOrFail();

        $item->update(['status_id' => $statusId]);

        session()->flash('message', __('site.status_updated'));
        $this->loadOrders();
    }

    public function render()
    {
        return view('livewire.delivery-agent-dashboard')->layout('layouts.app');
    }
}
