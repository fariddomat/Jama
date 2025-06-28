<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{

   public function index()
    {
        $user = Auth::user();

        $query = Item::query()->with(['order', 'order.customer', 'status']);

        if ($user->hasRole('delivery_agent')) {
            $query->whereHas('order', fn($q) => $q->where('delivery_agent_id', $user->id));
        } elseif ($user->hasRole('merchant')){
            $query->whereHas('order', fn($q) => $q->where('merchant_id', $user->id));
        }

        $items = $query->get();

        return view('dashboard.items.index', compact('items'));
    }
    public function create()
    {
                $orders = \App\Models\Order::all();
        $statuses = \App\Models\Status::all();

        return view('dashboard.items.create', compact([],'orders', 'statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255',
            'status_id' => 'required|exists:statuses,id'
        ]);

        $item = \App\Models\Item::create($validated);

        return redirect()->route('dashboard.items.index')->with('success', 'Item created successfully.');
    }

    public function show($id)
    {
        $item = \App\Models\Item::findOrFail($id);
                $orders = \App\Models\Order::all();
        $statuses = \App\Models\Status::all();

        return view('dashboard.items.show', compact('item'));
    }

    public function edit($id)
    {
        $item = \App\Models\Item::findOrFail($id);
                $orders = \App\Models\Order::all();
        $statuses = \App\Models\Status::all();

        return view('dashboard.items.edit', compact('item', 'orders', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $item = \App\Models\Item::findOrFail($id);
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255',
            'status_id' => 'required|exists:statuses,id'
        ]);

        $item->update($validated);

        return redirect()->route('dashboard.items.index')->with('success', 'Item updated successfully.');
    }

        public function destroy($id)
    {
        $item = \App\Models\Item::findOrFail($id);
        $item->delete();
        return redirect()->route('dashboard.items.index')->with('success', 'Item deleted successfully.');
    }
    public function restore($id)
    {
        $item = \App\Models\Item::withTrashed()->findOrFail($id);
        $item->restore();
        return redirect()->route('dashboard.items.index')->with('success', 'Item restored successfully.');
    }
}
