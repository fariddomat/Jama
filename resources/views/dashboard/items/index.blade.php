<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Item</h1>
        <a href="{{ route('dashboard.items.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded shadow" wire:navigate>➕ @lang('site.add') Item</a>

        <div class="overflow-x-auto mt-4">
            <x-autocrud::table
                :columns="['id', 'order_id', 'name', 'barcode', 'status_id']"
                :data="$items"
                routePrefix="dashboard.items"
                :show="true"
                :edit="true"
                :delete="true"
                :restore="true"
            />
        </div>
    </div>
</x-app-layout>