<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        $orderQuery = Order::query()->with(['status']);
        $customerQuery = Customer::query();

        if ($user->hasRole('merchant')) {
            $orderQuery->where('merchant_id', $user->id);
            $customerQuery->whereIn('id', Order::where('merchant_id', $user->id)->pluck('customer_id'));
        } elseif ($user->hasRole('delivery_agent')) {
            $orderQuery->where('delivery_agent_id', $user->id);
        }

        // Log queries for debugging
        Log::debug('Dashboard Order Query', ['query' => $orderQuery->toSql(), 'bindings' => $orderQuery->getBindings()]);
        Log::debug('Dashboard Customer Query', ['query' => $customerQuery->toSql(), 'bindings' => $customerQuery->getBindings()]);

        // Last Week Statistics
        $this->lastWeekStats = [
            'orders' => $this->getOrderStats($orderQuery->clone(), $lastWeekStart),
            'customers' => $user->hasRole('delivery_agent') ? null : $customerQuery->clone()->where('created_at', '>=', $lastWeekStart)->count(),
        ];

        // Last Month Statistics
        $this->lastMonthStats = [
            'orders' => $this->getOrderStats($orderQuery->clone(), $lastMonthStart),
            'customers' => $user->hasRole('delivery_agent') ? null : $customerQuery->clone()->where('created_at', '>=', $lastMonthStart)->count(),
        ];

        // Total Statistics
        $this->totalStats = [
            'orders' => $this->getOrderStats($orderQuery->clone()),
            'customers' => $user->hasRole('delivery_agent') ? null : $customerQuery->clone()->count(),
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
        try {
            if ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }

            $orders = $query->get();
            $byStatus = $orders->groupBy(function ($order) {
                return $order->status ? $order->status->name : 'Unknown';
            })->map->count()->toArray();

            return [
                'total' => $orders->count(),
                'by_status' => array_merge([
                    'Pending' => 0,
                    'Out for Delivery' => 0,
                    'Delivered' => 0,
                    'Not Delivered' => 0,
                    'Returned' => 0,
                    'Unknown' => 0,
                ], $byStatus),
            ];
        } catch (\Exception $e) {
            Log::error('Dashboard: getOrderStats failed', ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
            return [
                'total' => 0,
                'by_status' => [
                    'Pending' => 0,
                    'Out for Delivery' => 0,
                    'Delivered' => 0,
                    'Not Delivered' => 0,
                    'Returned' => 0,
                    'Unknown' => 0,
                ],
            ];
        }
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
