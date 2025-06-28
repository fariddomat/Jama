<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderImage;
use App\Models\Status;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ItemStatusUpdated;
use Endroid\QrCode\Reader\Reader;

class OrderStatusUpdater extends Component
{
    use WithFileUploads;

    public $barcode;
    public $orderNumber;
    public $image;
    public $status_id;
    public $item;
    public $order;
    public $mode = 'scan';
    public $statuses = [];

    public function mount()
    {
        $this->statuses = Status::whereNull('deleted_at')->pluck('name', 'id')->toArray();
    }

    protected $rules = [
        'barcode' => 'nullable|string',
        'orderNumber' => 'nullable|string',
        'image' => 'nullable|image|max:2048',
        'status_id' => 'required|exists:statuses,id',
    ];

    public function updatedMode($value)
    {
        $this->reset(['barcode', 'orderNumber', 'image', 'item', 'order', 'status_id']);
    }

    public function updatedBarcode($value)
    {
        if ($this->mode === 'scan' && $value) {
            $this->findItemOrOrder();
        }
    }

    public function findItemOrOrder()
    {
        $this->validate();

        $deliveryAgentId = auth()->user()->deliveryAgent ? auth()->user()->deliveryAgent->id : auth()->id();

        if ($this->mode === 'scan' && $this->barcode) {
            $this->item = Item::where('barcode', $this->barcode)
                ->whereHas('order', fn($query) => $query->where('delivery_agent_id', $deliveryAgentId))
                ->with('order')
                ->first();
            $this->order = $this->item ? $this->item->order : null;
        } elseif ($this->mode === 'manual' && $this->orderNumber) {
            $this->order = Order::where('id', $this->orderNumber)
                ->where('delivery_agent_id', $deliveryAgentId)
                ->first();
        } elseif ($this->mode === 'upload' && $this->image) {
            $path = $this->image->store('order_images', 'public');
            $orderImage = OrderImage::create([
                'order_id' => null,
                'path' => $path,
                'type' => 'barcode',
            ]);
            $this->item = $this->findItemByImage($path);
            $this->order = $this->item ? $this->item->order : null;
        }

        if (!$this->item && !$this->order) {
            $this->addError('order', __('site.order_not_found'));
        }
    }

    public function updateStatus()
    {
        $this->validate();

        if (auth()->user()) {
            if ($this->item) {
                $this->item->update(['status_id' => $this->status_id]);
                $order = $this->item->order;
            } elseif ($this->order) {
                $this->order->items()->update(['status_id' => $this->status_id]);
                $order = $this->order;
            }

            if ($this->image && $this->mode === 'upload') {
                $path = $this->image->store('order_images', 'public');
                OrderImage::create([
                    'order_id' => $order->id,
                    'path' => $path,
                    'type' => 'proof',
                ]);
            }

            // $order->customer->notify(new ItemStatusUpdated($order));

            session()->flash('message', __('site.status_updated'));
            $this->reset(['barcode', 'orderNumber', 'image', 'item', 'order', 'status_id']);
        }
    }

    private function findItemByImage($path)
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            $reader = new Reader();
            $result = $reader->decode($fullPath);
            $barcode = $result->getText();

            return Item::where('barcode', $barcode)
                ->whereHas('order', fn($query) => $query->where('delivery_agent_id', auth()->user()->deliveryAgent ? auth()->user()->deliveryAgent->id : auth()->id()))
                ->first();
        } catch (\Exception $e) {
            \Log::error('Barcode decode error: ' . $e->getMessage());
            return null;
        }
    }

    public function render()
    {
                return view('livewire.order-status-updater')->layout('layouts.app');;

    }
}
