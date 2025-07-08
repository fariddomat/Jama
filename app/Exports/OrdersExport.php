<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithHeadings
{
    protected $search;
    protected $deliveryAgent;
    protected $merchant;
    protected $status;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($search, $deliveryAgent, $merchant, $status, $dateFrom, $dateTo)
    {
        $this->search = $search;
        $this->deliveryAgent = $deliveryAgent;
        $this->merchant = $merchant;
        $this->status = $status;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        $query = Order::with(['customer', 'merchant', 'deliveryAgent', 'items.status'])
            ->when($this->search, fn($q) => $q->where('otp', 'like', "%{$this->search}%")
                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$this->search}%")))
            ->when($this->deliveryAgent, fn($q) => $q->whereHas('deliveryAgent', fn($q) => $q->where('id', $this->deliveryAgent)))
            ->when($this->merchant, fn($q) => $q->whereHas('merchant', fn($q) => $q->where('id', $this->merchant)))
            ->when($this->status, fn($q) => $q->whereHas('items', fn($q) => $q->where('status_id', $this->status)))
            ->when($this->dateFrom, fn($q) => $q->whereDate('delivery_time', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('delivery_time', '<=', $this->dateTo));

        return $query->get()->map(function ($order) {
            $status = $order->items->first() ? $order->items->first()->status->name : '—';
            return [
                'ID' => $order->id,
                'Customer' => $order->customer ? $order->customer->name : '—',
                'Merchant' => $order->merchant ? $order->merchant->name : '—',
                'Delivery Agent' => $order->deliveryAgent ? $order->deliveryAgent->name : '—',
                'From Address' => $order->from_address,
                'To Address' => $order->to_address,
                'Delivery Time' => $order->delivery_time,
                'OTP' => $order->otp,
                'Status' => $status,
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'Customer', 'Merchant', 'Delivery Agent', 'From Address', 'To Address', 'Delivery Time', 'OTP', 'Status'];
    }
}
