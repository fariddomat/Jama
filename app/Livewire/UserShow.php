<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
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
        try {
            $this->user = User::with(['merchantOrders.customer', 'assignedOrders.customer'])->findOrFail($id);
            Log::info('UserShow: User loaded', ['user_id' => $id]);
            // $this->authorize('view', $this->user);
            $this->loadStatistics();
        } catch (\Exception $e) {
            Log::error('UserShow: Mount failed', ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    private function loadStatistics()
    {
        $lastWeekStart = Carbon::now()->subDays(7)->startOfDay();
        $lastMonthStart = Carbon::now()->subDays(30)->startOfDay();

        $orderQuery = Order::query()->with(['status']);
        $customerQuery = Customer::query();

        if ($this->user->hasRole('merchant')) {
            $orderQuery->where('merchant_id', $this->user->id);
            $customerQuery->whereIn('id', Order::where('merchant_id', $this->user->id)->pluck('customer_id'));
        } elseif ($this->user->hasRole('delivery_agent')) {
            $orderQuery->where('delivery_agent_id', $this->user->id);
        }

        // Log queries for debugging
        Log::debug('User Show Order Query', ['query' => $orderQuery->toSql(), 'bindings' => $orderQuery->getBindings()]);
        Log::debug('User Show Customer Query', ['query' => $customerQuery->toSql(), 'bindings' => $customerQuery->getBindings()]);

        // Last Week Statistics
        $this->lastWeekStats = [
            'orders' => $this->getOrderStats($orderQuery->clone(), $lastWeekStart),
            'customers' => $this->user->hasRole('delivery_agent') ? null : $customerQuery->clone()->where('created_at', '>=', $lastWeekStart)->count(),
        ];

        // Last Month Statistics
        $this->lastMonthStats = [
            'orders' => $this->getOrderStats($orderQuery->clone(), $lastMonthStart),
            'customers' => $this->user->hasRole('delivery_agent') ? null : $customerQuery->clone()->where('created_at', '>=', $lastMonthStart)->count(),
        ];

        // Total Statistics
        $this->totalStats = [
            'orders' => $this->getOrderStats($orderQuery->clone()),
            'customers' => $this->user->hasRole('delivery_agent') ? null : $customerQuery->clone()->count(),
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
            Log::error('UserShow: getOrderStats failed', ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
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
