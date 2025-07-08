<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Imports\OrdersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function create()
    {
        $customers = \App\Models\Customer::all();
        $merchants = \App\Models\User::whereRole('merchant')->get();
        $deliveryAgents = \App\Models\User::whereRole('delivery_agent')->get();

        return view('dashboard.orders.create', compact('customers', 'merchants', 'deliveryAgents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'merchant_id' => 'required|exists:users,id',
            'delivery_agent_id' => 'nullable|exists:users,id',
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'delivery_time' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $order = \App\Models\Order::create($validated);

        return redirect()->route('dashboard.orders.index')->with('success', 'Order created successfully.');
    }

    public function show($id)
    {
        $order = \App\Models\Order::with(['customer', 'merchant', 'deliveryAgent', 'items'])->findOrFail($id);
        $this->authorize('view', $order);

        return view('dashboard.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $this->authorize('update', $order);

        $customers = \App\Models\Customer::all();
        $merchants = \App\Models\User::whereRole('merchant')->get();
        $deliveryAgents = \App\Models\User::whereRole('delivery_agent')->get();

        return view('dashboard.orders.edit', compact('order', 'customers', 'merchants', 'deliveryAgents'));
    }

    public function update(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $this->authorize('update', $order);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'merchant_id' => 'required|exists:users,id',
            'delivery_agent_id' => 'nullable|exists:users,id',
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'delivery_time' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('dashboard.orders.index')->with('success', 'Order updated successfully.');
    }

    public function destroy($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $this->authorize('delete', $order);

        $order->delete();
        return redirect()->route('dashboard.orders.index')->with('success', 'Order deleted successfully.');
    }

    public function restore($id)
    {
        $order = \App\Models\Order::withTrashed()->findOrFail($id);
        $this->authorize('restore', $order);

        $order->restore();
        return redirect()->route('dashboard.orders.index')->with('success', 'Order restored successfully.');
    }

    public function import()
    {
        return view('dashboard.orders.import', ['skippedRows' => session('skippedRows', [])]);
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $import = new OrdersImport();
            Excel::import($import, $request->file('file'));
            $skippedRows = $import->getSkippedRows();

            if (!empty($skippedRows)) {
                return redirect()->route('dashboard.orders.import')
                    ->with('warning', __('Some rows were skipped during import.'))
                    ->with('skippedRows', $skippedRows);
            }

            return redirect()->route('dashboard.orders.index')->with('success', __('Orders imported successfully.'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Import failed', ['error' => $e->getMessage()]);
            return redirect()->route('dashboard.orders.import')
                ->with('error', __('Error importing orders: :message', ['message' => $e->getMessage()]));
        }
    }
}
