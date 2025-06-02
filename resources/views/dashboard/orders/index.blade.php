<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Order</h1>
        <a href="{{ route('dashboard.orders.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded shadow" wire:navigate>➕ @lang('site.add') Order</a>

        <div class="overflow-x-auto mt-4">
            <x-autocrud::table
                :columns="['id', 'customer_id', 'merchant_id', 'delivery_agent_id', 'from_address', 'to_address', 'delivery_time']"
                :data="$orders"
                routePrefix="dashboard.orders"
                :show="true"
                :edit="true"
                :delete="true"
                :restore="true"
            />
        </div>
    </div>
</x-app-layout>