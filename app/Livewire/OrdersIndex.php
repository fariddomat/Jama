<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Status;
use App\Models\User;
use Maatwebsite\Excel\Excel;
use App\Exports\OrdersExport;
use Illuminate\Support\Facades\Auth;

class OrdersIndex extends Component
{
    public $search = '';
    public $deliveryAgent = '';
    public $merchant = '';
    public $status = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = ['search', 'deliveryAgent', 'merchant', 'status', 'dateFrom', 'dateTo'];

    public function render()
    {
        $user = Auth::user();
        $orders = Order::with(['customer', 'merchant', 'deliveryAgent', 'status'])
            ->when($user->hasRole('delivery_agent'), fn($q) => $q->where('delivery_agent_id', $user->id))
            ->when($user->hasRole('merchant'), fn($q) => $q->where('merchant_id', $user->id))
            ->when($this->search, fn($q) => $q->where('otp', 'like', "%{$this->search}%")
                ->orWhereHas('customer', fn($q) => $q->where('name', 'like', "%{$this->search}%")))
            ->when($this->deliveryAgent && ($user->hasRole('superadministrator') || $user->hasRole('merchant')),
                fn($q) => $q->whereHas('deliveryAgent', fn($q) => $q->where('id', $this->deliveryAgent)))
            ->when($this->merchant && $user->hasRole('superadministrator'),
                fn($q) => $q->whereHas('merchant', fn($q) => $q->where('id', $this->merchant)))
            ->when($this->status, fn($q) => $q->where('status_id', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('delivery_time', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('delivery_time', '<=', $this->dateTo))
            ->get();

        $deliveryAgents = ($user->hasRole('superadministrator') || $user->hasRole('merchant'))
            ? User::whereRole('delivery_agent')->pluck('name', 'id')
            : collect();
        $merchants = $user->hasRole('superadministrator')
            ? User::whereRole('merchant')->pluck('name', 'id')
            : collect();
        $statuses = Status::whereNull('deleted_at')->pluck('name', 'id');

        return view('livewire.orders-index', compact('orders', 'deliveryAgents', 'merchants', 'statuses'))
            ->layout('layouts.app');
    }

    public function export()
    {
        $filename = 'orders_' . now()->format('Y-m-d') . '.csv';
        return \Maatwebsite\Excel\Facades\Excel::download(
            new OrdersExport($this->search, $this->deliveryAgent, $this->merchant, $this->status, $this->dateFrom, $this->dateTo),
            $filename,
            Excel::CSV
        );
    }
}
