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
        if (empty($row['phone_number']) || empty($row['customer_name']) || empty($row['address']) || empty($row['status'])) {
            Log::warning('Skipping row due to missing required columns', ['row' => $row]);
            $this->skippedRows[] = ['row' => $row, 'reason' => 'Missing required columns: phone_number, customer_name, address, or status'];
            return null;
        }

        // Find or create customer by phone_number (unique mobile)
        try {
            $customer = Customer::firstOrCreate(
                ['mobile' => $row['phone_number']],
                [
                    'name' => $row['customer_name'],
                    'address' => $row['address'],
                ]
            );
        } catch (\Exception $e) {
            Log::warning('Skipping row due to invalid customer data', ['phone_number' => $row['phone_number'], 'error' => $e->getMessage()]);
            $this->skippedRows[] = ['row' => $row, 'reason' => 'Invalid customer data: ' . $e->getMessage()];
            return null;
        }

        // Determine merchant based on authenticated user's role
        $user = Auth::user();
        $merchant = null;
        if ($user->hasRole('merchant')) {
            $merchant = $user;
        } elseif ($user->hasRole('superadministrator')) {
            // Find merchant by doctor_name
            $merchant = \App\Models\User::where('name', $row['doctor_name'])->first();
            if (!$merchant) {
                Log::warning('Skipping row due to invalid merchant', ['doctor_name' => $row['doctor_name'], 'row' => $row]);
                $this->skippedRows[] = ['row' => $row, 'reason' => 'Invalid merchant: ' . ($row['doctor_name'] ?? 'N/A')];
                return null;
            }
        } else {
            Log::warning('Skipping row due to unauthorized user', ['user_id' => $user->id, 'row' => $row]);
            $this->skippedRows[] = ['row' => $row, 'reason' => 'Unauthorized user'];
            return null;
        }

        // Validate and find status
        $validStatuses = ['Pending', 'Out for Delivery', 'Delivered', 'Not Delivered', 'Returned'];
        if (!in_array($row['status'], $validStatuses)) {
            Log::warning('Skipping row due to invalid status', ['status' => $row['status'], 'row' => $row]);
            $this->skippedRows[] = ['row' => $row, 'reason' => 'Invalid status: ' . ($row['status'] ?? 'N/A')];
            return null;
        }

        $status = Status::firstOrCreate(['name' => $row['status']]);

        // Create order
        try {
            $order = Order::create([
                'customer_id' => $customer->id,
                'merchant_id' => $merchant->id,
                'delivery_agent_id' => null,
                'status_id' => $status->id,
                'from_address' => $merchant->address ?? ($row['from_address'] ?? ''),
                'to_address' => $row['address'],
                'delivery_time' => $row['delivery_time'] ? \Carbon\Carbon::parse($row['delivery_time']) : now(),
                'otp' => $row['otp'] ?? Str::random(10),
                'notes' => $row['notes'] ?? null,
            ]);

            // Create items
            if (!empty($row['items'])) {
                $items = explode(',', $row['items']);
                foreach ($items as $itemName) {
                    Item::create([
                        'order_id' => $order->id,
                        'name' => trim($itemName),
                        'barcode' => $row['barcode'] ?? Str::random(12),
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
