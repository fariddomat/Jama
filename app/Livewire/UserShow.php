<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserStatsExport;

class UserShow extends Component
{
    public $user;
    public $lastWeekStats = [];
    public $lastMonthStats = [];
    public $totalStats = [];

    public function mount($id)
    {
        $this->user = User::with(['merchantOrders.customer', 'assignedOrders.customer'])->findOrFail($id);
        $this->loadStatistics();
    }

    private function loadStatistics()
    {
        $lastWeekStart = Carbon::now()->subDays(7)->startOfDay();
        $lastMonthStart = Carbon::now()->subDays(30)->startOfDay();

        $orderQuery = Order::query()->with('items.status');
        $customerQuery = Customer::query();
        $itemQuery = Item::query()->with('status');

        if ($this->user->hasRole('merchant')) {
            $orderQuery->where('merchant_id', $this->user->id);
            $customerQuery->whereIn('id', Order::where('merchant_id', $this->user->id)->pluck('customer_id'));
            $itemQuery->whereIn('order_id', Order::where('merchant_id', $this->user->id)->pluck('id'));
        } elseif ($this->user->hasRole('delivery_agent')) {
            $orderQuery->where('delivery_agent_id', $this->user->id);
            $itemQuery->whereIn('order_id', Order::where('delivery_agent_id', $this->user->id)->pluck('id'));
        }

        // Log queries for debugging
        Log::debug('User Show Order Query', ['query' => $orderQuery->toSql(), 'bindings' => $orderQuery->getBindings()]);
        Log::debug('User Show Customer Query', ['query' => $customerQuery->toSql(), 'bindings' => $customerQuery->getBindings()]);
        Log::debug('User Show Item Query', ['query' => $itemQuery->toSql(), 'bindings' => $itemQuery->getBindings()]);

        // Last Week Statistics
        $this->lastWeekStats = [
            'orders' => $this->getOrderStats($orderQuery->clone(), $lastWeekStart),
            'customers' => $this->user->hasRole('delivery_agent') ? null : $customerQuery->clone()->where('created_at', '>=', $lastWeekStart)->count(),
            'items' => $this->getItemStats($itemQuery->clone(), $lastWeekStart),
        ];

        // Last Month Statistics
        $this->lastMonthStats = [
            'orders' => $this->getOrderStats($orderQuery->clone(), $lastMonthStart),
            'customers' => $this->user->hasRole('delivery_agent') ? null : $customerQuery->clone()->where('created_at', '>=', $lastMonthStart)->count(),
            'items' => $this->getItemStats($itemQuery->clone(), $lastMonthStart),
        ];

        // Total Statistics
        $this->totalStats = [
            'orders' => $this->getOrderStats($orderQuery->clone()),
            'customers' => $this->user->hasRole('delivery_agent') ? null : $customerQuery->clone()->count(),
            'items' => $this->getItemStats($itemQuery->clone()),
        ];

        // Log stats for debugging
        Log::debug('User Show Stats', [
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

    public function export()
    {
        $filename = 'user_stats_' . $this->user->id . '_' . now()->format('Y-m-d') . '.csv';
        return Excel::download(
            new UserStatsExport($this->user, $this->totalStats, $this->lastWeekStats, $this->lastMonthStats),
            $filename,
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    public function render()
    {
        return view('livewire.user-show')->layout('layouts.app');
    }
}
