<?php

namespace App\Imports;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrdersImport implements ToModel, WithHeadingRow
{
    protected $skippedRows = [];

    public function model(array $row)
    {
        // Validate required columns
        if (empty($row['mr_number']) || empty($row['customer_name']) || empty($row['phone_number']) || empty($row['address']) || empty($row['status'])) {
            Log::warning('Skipping row due to missing required columns', ['row' => $row]);
            $this->skippedRows[] = ['row' => $row, 'reason' => 'Missing required columns'];
            return null;
        }

        // Find or create customer by MR Number
        $customer = Customer::firstOrCreate(
            ['mr_number' => $row['mr_number']],
            [
                'name' => $row['customer_name'],
                'mobile' => $row['phone_number'],
                'address' => $row['address'],
            ]
        );

        // Determine merchant based on authenticated user's role
        $user = Auth::user();
        if ($user->hasRole('merchant')) {
            $merchant = $user;
        } else {
            // For superadministrator, find merchant by doctor_name
            $merchant = \App\Models\User::where('name', $row['doctor_name'])->first();
            if (!$merchant) {
                Log::warning('Skipping row due to invalid merchant', ['doctor_name' => $row['doctor_name'], 'row' => $row]);
                $this->skippedRows[] = ['row' => $row, 'reason' => 'Invalid merchant: ' . $row['doctor_name']];
                return null;
            }
        }

        // Validate status
        $validStatuses = ['Pending', 'Out for Delivery', 'Delivered', 'Not Delivered', 'Returned'];
        if (!in_array($row['status'], $validStatuses)) {
            Log::warning('Skipping row due to invalid status', ['status' => $row['status'], 'row' => $row]);
            $this->skippedRows[] = ['row' => $row, 'reason' => 'Invalid status: ' . $row['status']];
            return null;
        }

        // Find or create status
        $status = Status::firstOrCreate(['name' => $row['status']]);

        // Create order
        try {
            $order = Order::create([
                'customer_id' => $customer->id,
                'merchant_id' => $merchant->id,
                'delivery_agent_id' => null,
                'from_address' => $merchant->address ?? '',
                'to_address' => $row['address'],
                'delivery_time' => now(), // Adjust if Excel provides a date
                'otp' => Str::random(10),
                'notes' => $row['notes'] ?? null,
            ]);

            // Create items
            if (!empty($row['items'])) {
                $items = explode(',', $row['items']);
                foreach ($items as $itemName) {
                    Item::create([
                        'order_id' => $order->id,
                        'name' => trim($itemName),
                        'barcode' => Str::random(12),
                        'status_id' => $status->id,
                    ]);
                }
            }

            return $order;
        } catch (\Exception $e) {
            Log::error('Failed to create order or items', ['error' => $e->getMessage(), 'row' => $row]);
            $this->skippedRows[] = ['row' => $row, 'reason' => 'Failed to create order: ' . $e->getMessage()];
            return null;
        }
    }

    public function getSkippedRows()
    {
        return $this->skippedRows;
    }
}
