<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserStatsExport implements FromCollection, WithHeadings
{
    protected $user;
    protected $totalStats;
    protected $lastWeekStats;
    protected $lastMonthStats;

    public function __construct(User $user, array $totalStats, array $lastWeekStats, array $lastMonthStats)
    {
        $this->user = $user;
        $this->totalStats = $totalStats;
        $this->lastWeekStats = $lastWeekStats;
        $this->lastMonthStats = $lastMonthStats;
    }

    public function collection()
    {
        $data = [
            [
                'Section' => 'User Details',
                'Name' => $this->user->name,
                'Email' => $this->user->email ?? '—',
                'Contact Number' => $this->user->contact_number ?? '—',
                'Address' => $this->user->address ?? '—',
                'Active' => $this->user->active ? 'Yes' : 'No',
                'Role' => $this->user->roles->pluck('name')->implode(', '),
            ],
            [
                'Section' => 'Total Statistics',
                'Orders' => $this->totalStats['orders']['total'],
                'Pending Orders' => $this->totalStats['orders']['pending'],
                'Out for Delivery Orders' => $this->totalStats['orders']['out_for_delivery'],
                'Delivered Orders' => $this->totalStats['orders']['delivered'],
                'Not Delivered Orders' => $this->totalStats['orders']['not_delivered'],
                'Returned Orders' => $this->totalStats['orders']['returned'],
                'Customers' => $this->user->hasRole('delivery_agent') ? 'N/A' : $this->totalStats['customers'],
                'Items' => $this->totalStats['items']['total'],
                'Items Pending' => $this->totalStats['items']['by_status']['Pending'],
                'Items Out for Delivery' => $this->totalStats['items']['by_status']['Out for Delivery'],
                'Items Delivered' => $this->totalStats['items']['by_status']['Delivered'],
                'Items Not Delivered' => $this->totalStats['items']['by_status']['Not Delivered'],
                'Items Returned' => $this->totalStats['items']['by_status']['Returned'],
            ],
            [
                'Section' => 'Last Week Statistics',
                'Orders' => $this->lastWeekStats['orders']['total'],
                'Pending Orders' => $this->lastWeekStats['orders']['pending'],
                'Out for Delivery Orders' => $this->lastWeekStats['orders']['out_for_delivery'],
                'Delivered Orders' => $this->lastWeekStats['orders']['delivered'],
                'Not Delivered Orders' => $this->lastWeekStats['orders']['not_delivered'],
                'Returned Orders' => $this->lastWeekStats['orders']['returned'],
                'Customers' => $this->user->hasRole('delivery_agent') ? 'N/A' : $this->lastWeekStats['customers'],
                'Items' => $this->lastWeekStats['items']['total'],
                'Items Pending' => $this->lastWeekStats['items']['by_status']['Pending'],
                'Items Out for Delivery' => $this->lastWeekStats['items']['by_status']['Out for Delivery'],
                'Items Delivered' => $this->lastWeekStats['items']['by_status']['Delivered'],
                'Items Not Delivered' => $this->lastWeekStats['items']['by_status']['Not Delivered'],
                'Items Returned' => $this->lastWeekStats['items']['by_status']['Returned'],
            ],
            [
                'Section' => 'Last Month Statistics',
                'Orders' => $this->lastMonthStats['orders']['total'],
                'Pending Orders' => $this->lastMonthStats['orders']['pending'],
                'Out for Delivery Orders' => $this->lastMonthStats['orders']['out_for_delivery'],
                'Delivered Orders' => $this->lastMonthStats['orders']['delivered'],
                'Not Delivered Orders' => $this->lastMonthStats['orders']['not_delivered'],
                'Returned Orders' => $this->lastMonthStats['orders']['returned'],
                'Customers' => $this->user->hasRole('delivery_agent') ? 'N/A' : $this->lastMonthStats['customers'],
                'Items' => $this->lastMonthStats['items']['total'],
                'Items Pending' => $this->lastMonthStats['items']['by_status']['Pending'],
                'Items Out for Delivery' => $this->lastMonthStats['items']['by_status']['Out for Delivery'],
                'Items Delivered' => $this->lastMonthStats['items']['by_status']['Delivered'],
                'Items Not Delivered' => $this->lastMonthStats['items']['by_status']['Not Delivered'],
                'Items Returned' => $this->lastMonthStats['items']['by_status']['Returned'],
            ],
        ];

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Section',
            'Name',
            'Email',
            'Contact Number',
            'Address',
            'Active',
            'Role',
            'Orders',
            'Pending Orders',
            'Out for Delivery Orders',
            'Delivered Orders',
            'Not Delivered Orders',
            'Returned Orders',
            'Customers',
            'Items',
            'Items Pending',
            'Items Out for Delivery',
            'Items Delivered',
            'Items Not Delivered',
            'Items Returned',
        ];
    }
}
