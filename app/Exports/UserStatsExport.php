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
                'Orders' => $this->totalStats['orders']['total'] ?? 0,
                'Pending Orders' => $this->totalStats['orders']['by_status']['Pending'] ?? 0,
                'Out for Delivery Orders' => $this->totalStats['orders']['by_status']['Out for Delivery'] ?? 0,
                'Delivered Orders' => $this->totalStats['orders']['by_status']['Delivered'] ?? 0,
                'Not Delivered Orders' => $this->totalStats['orders']['by_status']['Not Delivered'] ?? 0,
                'Returned Orders' => $this->totalStats['orders']['by_status']['Returned'] ?? 0,
                'Unknown Orders' => $this->totalStats['orders']['by_status']['Unknown'] ?? 0,
                'Customers' => $this->user->hasRole('delivery_agent') ? 'N/A' : ($this->totalStats['customers'] ?? 0),
            ],
            [
                'Section' => 'Last Week Statistics',
                'Orders' => $this->lastWeekStats['orders']['total'] ?? 0,
                'Pending Orders' => $this->lastWeekStats['orders']['by_status']['Pending'] ?? 0,
                'Out for Delivery Orders' => $this->lastWeekStats['orders']['by_status']['Out for Delivery'] ?? 0,
                'Delivered Orders' => $this->lastWeekStats['orders']['by_status']['Delivered'] ?? 0,
                'Not Delivered Orders' => $this->lastWeekStats['orders']['by_status']['Not Delivered'] ?? 0,
                'Returned Orders' => $this->lastWeekStats['orders']['by_status']['Returned'] ?? 0,
                'Unknown Orders' => $this->lastWeekStats['orders']['by_status']['Unknown'] ?? 0,
                'Customers' => $this->user->hasRole('delivery_agent') ? 'N/A' : ($this->lastWeekStats['customers'] ?? 0),
            ],
            [
                'Section' => 'Last Month Statistics',
                'Orders' => $this->lastMonthStats['orders']['total'] ?? 0,
                'Pending Orders' => $this->lastMonthStats['orders']['by_status']['Pending'] ?? 0,
                'Out for Delivery Orders' => $this->lastMonthStats['orders']['by_status']['Out for Delivery'] ?? 0,
                'Delivered Orders' => $this->lastMonthStats['orders']['by_status']['Delivered'] ?? 0,
                'Not Delivered Orders' => $this->lastMonthStats['orders']['by_status']['Not Delivered'] ?? 0,
                'Returned Orders' => $this->lastMonthStats['orders']['by_status']['Returned'] ?? 0,
                'Unknown Orders' => $this->lastMonthStats['orders']['by_status']['Unknown'] ?? 0,
                'Customers' => $this->user->hasRole('delivery_agent') ? 'N/A' : ($this->lastMonthStats['customers'] ?? 0),
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
            'Unknown Orders',
            'Customers',
        ];
    }
}
