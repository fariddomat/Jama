<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;

class OrderController extends Controller
{

    public function index()
    {
        $orders = \App\Models\Order::all();
        return view('dashboard.orders.index', compact('orders'));
    }

    public function create()
    {
                $customers = \App\Models\Customer::all();
        $merchants = \App\Models\User::all();
        $deliveryAgents = \App\Models\User::all();

        return view('dashboard.orders.create', compact([],'customers', 'merchants', 'deliveryAgents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'merchant_id' => 'required|exists:merchants,id',
            'delivery_agent_id' => 'required|exists:delivery_agents,id',
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'delivery_time' => 'required|date'
        ]);

        $order = \App\Models\Order::create($validated);

        return redirect()->route('dashboard.orders.index')->with('success', 'Order created successfully.');
    }

    public function show($id)
    {
        $order = \App\Models\Order::findOrFail($id);
                $customers = \App\Models\Customer::all();
        $merchants = \App\Models\User::all();
        $deliveryAgents = \App\Models\User::all();

        return view('dashboard.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = \App\Models\Order::findOrFail($id);
                $customers = \App\Models\Customer::all();
        $merchants = \App\Models\User::all();
        $deliveryAgents = \App\Models\User::all();

        return view('dashboard.orders.edit', compact('order', 'customers', 'merchants', 'deliveryAgents'));
    }

    public function update(Request $request, $id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'merchant_id' => 'required|exists:merchants,id',
            'delivery_agent_id' => 'required|exists:delivery_agents,id',
            'from_address' => 'required|string',
            'to_address' => 'required|string',
            'delivery_time' => 'required|date'
        ]);

        $order->update($validated);

        return redirect()->route('dashboard.orders.index')->with('success', 'Order updated successfully.');
    }

        public function destroy($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->delete();
        return redirect()->route('dashboard.orders.index')->with('success', 'Order deleted successfully.');
    }
    public function restore($id)
    {
        $order = \App\Models\Order::withTrashed()->findOrFail($id);
        $order->restore();
        return redirect()->route('dashboard.orders.index')->with('success', 'Order restored successfully.');
    }
}
