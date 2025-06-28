<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Dashboard extends Component
{
    public $lastWeekStats = [];
    public $lastMonthStats = [];
    public $totalStats = [];

    public function mount()
    {
        $user = Auth::user();
        $this->loadStatistics($user);
    }

    private function loadStatistics($user)
    {
        $lastWeekStart = Carbon::now()->subDays(7)->startOfDay();
        $lastMonthStart = Carbon::now()->subDays(30)->startOfDay();

        $orderQuery = Order::query()->with('items.status');
        $customerQuery = Customer::query();
        $itemQuery = Item::query()->with('status');

        if ($user->hasRole('merchant')) {
            $orderQuery->where('merchant_id', $user->id);
            $customerQuery->whereIn('id', Order::where('merchant_id', $user->id)->pluck('customer_id'));
            $itemQuery->whereIn('order_id', Order::where('merchant_id', $user->id)->pluck('id'));
        } elseif ($user->hasRole('delivery_agent')) {
            $orderQuery->where('delivery_agent_id', $user->id);
            $itemQuery->whereIn('order_id', Order::where('delivery_agent_id', $user->id)->pluck('id'));
        }

        // Log queries for debugging
        Log::debug('Order Query', ['query' => $orderQuery->toSql(), 'bindings' => $orderQuery->getBindings()]);
        Log::debug('Customer Query', ['query' => $customerQuery->toSql(), 'bindings' => $customerQuery->getBindings()]);
        Log::debug('Item Query', ['query' => $itemQuery->toSql(), 'bindings' => $itemQuery->getBindings()]);

        // Last Week Statistics
        $this->lastWeekStats = [
            'orders' => $this->getOrderStats($orderQuery->clone(), $lastWeekStart),
            'customers' => $user->hasRole('delivery_agent') ? null : $customerQuery->clone()->where('created_at', '>=', $lastWeekStart)->count(),
            'items' => $this->getItemStats($itemQuery->clone(), $lastWeekStart),
        ];

        // Last Month Statistics
        $this->lastMonthStats = [
            'orders' => $this->getOrderStats($orderQuery->clone(), $lastMonthStart),
            'customers' => $user->hasRole('delivery_agent') ? null : $customerQuery->clone()->where('created_at', '>=', $lastMonthStart)->count(),
            'items' => $this->getItemStats($itemQuery->clone(), $lastMonthStart),
        ];

        // Total Statistics
        $this->totalStats = [
            'orders' => $this->getOrderStats($orderQuery->clone()),
            'customers' => $user->hasRole('delivery_agent') ? null : $customerQuery->clone()->count(),
            'items' => $this->getItemStats($itemQuery->clone()),
        ];

        // Log stats for debugging
        Log::debug('Dashboard Stats', [
            'lastWeekStats' => $this->lastWeekStats,
            'lastMonthStats' => $this->lastMonthStats,
            'totalStats' => $this->totalStats,
        ]);
    }

    private function getOrderStats($query, $startDate = null)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $statuses = ['Pending', 'Out for Delivery', 'Delivered', 'Not Delivered', 'Returned'];
        $stats = [
            'total' => $query->count(),
            'pending' => 0,
            'out_for_delivery' => 0,
            'delivered' => 0,
            'not_delivered' => 0,
            'returned' => 0,
        ];

        foreach ($statuses as $status) {
            $key = Str::snake($status);
            $stats[$key] = $query->clone()->whereHas('items', function ($q) use ($status) {
                $q->whereHas('status', function ($s) use ($status) {
                    $s->where('name', $status);
                });
            })->count();
        }

        return $stats;
    }

    private function getItemStats($query, $startDate = null)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $items = $query->get();
        $byStatus = $items->groupBy('status.name')->map->count()->toArray();

        return [
            'total' => $items->count(),
            'by_status' => array_merge([
                'Pending' => 0,
                'Out for Delivery' => 0,
                'Delivered' => 0,
                'Not Delivered' => 0,
                'Returned' => 0,
            ], $byStatus),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
