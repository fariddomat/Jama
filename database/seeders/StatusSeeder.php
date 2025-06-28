<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            'Pending',
            'Out for Delivery',
            'Delivered',
            'Not Delivered',
            'Returned',
        ];

        foreach ($statuses as $statusName) {
            Status::firstOrCreate(
                ['name' => $statusName],
                ['name' => $statusName]
            );
        }
    }
}
