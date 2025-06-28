<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\OrderImage;

class OrderImageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $orderImages = \App\Models\OrderImage::all();
        return view('dashboard.order_images.index', compact('orderImages'));
    }

    public function create()
    {
                $orders = \App\Models\Order::all();

        return view('dashboard.order_images.create', compact([],'orders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'path' => 'required|string|max:255',
            'type' => 'required|string|max:255'
        ]);

        $orderImage = \App\Models\OrderImage::create($validated);

        return redirect()->route('dashboard.order_images.index')->with('success', 'OrderImage created successfully.');
    }

    public function show($id)
    {
        $orderImage = \App\Models\OrderImage::findOrFail($id);
                $orders = \App\Models\Order::all();

        return view('dashboard.order_images.show', compact('orderImage'));
    }

    public function edit($id)
    {
        $orderImage = \App\Models\OrderImage::findOrFail($id);
                $orders = \App\Models\Order::all();

        return view('dashboard.order_images.edit', compact('orderImage', 'orders'));
    }

    public function update(Request $request, $id)
    {
        $orderImage = \App\Models\OrderImage::findOrFail($id);
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'path' => 'required|string|max:255',
            'type' => 'required|string|max:255'
        ]);

        $orderImage->update($validated);

        return redirect()->route('dashboard.order_images.index')->with('success', 'OrderImage updated successfully.');
    }

        public function destroy($id)
    {
        $orderImage = \App\Models\OrderImage::findOrFail($id);
        $orderImage->delete();
        return redirect()->route('dashboard.order_images.index')->with('success', 'OrderImage deleted successfully.');
    }
    public function restore($id)
    {
        $orderImage = \App\Models\OrderImage::withTrashed()->findOrFail($id);
        $orderImage->restore();
        return redirect()->route('dashboard.order_images.index')->with('success', 'OrderImage restored successfully.');
    }
}
