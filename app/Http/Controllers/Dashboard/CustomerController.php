<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Customer::query()->with('orders');

        if ($user->hasRole('merchant')) {
            $query->whereHas('orders', fn($q) => $q->where('merchant_id', $user->id));
        } elseif (!$user->hasRole('superadministrator')) {
            abort(403, 'Unauthorized');
        }

        $customers = $query->get();

        return view('dashboard.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('dashboard.customers.create');
    }

    public function store(Request $request)
    {
        // dd(request()->mr_number);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255|unique:customers,mobile',
            'mr_number' => 'required|string|max:255|unique:customers,mr_number',
            'address' => 'required|string',
        ]);

        // dd($request->mr_number);
        $customer = \App\Models\Customer::create($validated);

        return redirect()->route('dashboard.customers.index')->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        $customer = \App\Models\Customer::findOrFail($id);

        return view('dashboard.customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = \App\Models\Customer::findOrFail($id);

        return view('dashboard.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:255|unique:customers,mobile,' . $customer->id,
            'address' => 'required|string',
            'mr_number' => 'required|string|max:255|unique:customers,mr_number,' . $customer->id,
        ]);

        $customer->update($validated);

        return redirect()->route('dashboard.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('dashboard.customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function restore($id)
    {
        $customer = \App\Models\Customer::withTrashed()->findOrFail($id);
        $customer->restore();
        return redirect()->route('dashboard.customers.index')->with('success', 'Customer restored successfully.');
    }
}
