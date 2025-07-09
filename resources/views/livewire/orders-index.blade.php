<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Orders</h1>
    <div class="flex justify-between mb-4">
        @if (!auth()->user()->hasRole('delivery_agent'))
            <div class="space-x-2">
                <a href="{{ route('dashboard.orders.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600" wire:navigate>
                    ➕ Add Order
                </a>
                <a href="{{ route('dashboard.orders.import') }}" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600" wire:navigate>
                    📤 Import Orders
                </a>
            </div>
        @endif
        <button wire:click="export" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
            📤 Export
        </button>
    </div>

    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <input wire:model.live="search" type="text" placeholder="Search..." class="border-gray-300 rounded-md shadow-sm p-2">
        @if (auth()->user()->hasRole('superadministrator') || auth()->user()->hasRole('merchant'))
            <select wire:model.live="deliveryAgent" class="border-gray-300 rounded-md shadow-sm p-2">
                <option value="">All Delivery Agents</option>
                @foreach ($deliveryAgents as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        @endif
        @if (auth()->user()->hasRole('superadministrator'))
            <select wire:model.live="merchant" class="border-gray-300 rounded-md shadow-sm p-2">
                <option value="">All Merchants</option>
                @foreach ($merchants as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        @endif
        <select wire:model.live="status" class="border-gray-300 rounded-md shadow-sm p-2">
            <option value="">All Statuses</option>
            @foreach ($statuses as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
        <input wire:model.live="dateFrom" type="date" class="border-gray-300 rounded-md shadow-sm p-2">
        <input wire:model.live="dateTo" type="date" class="border-gray-300 rounded-md shadow-sm p-2">
    </div>

    <div class="overflow-x-auto mt-4">
        <x-table
            :columns="['id', 'customer_id', 'merchant_id', 'delivery_agent_id', 'delivery_time', 'otp', 'statuss']"
            :data="$orders"
            routePrefix="dashboard.orders"
            :show="true"
            :edit="true"
            :delete="true"
            :restore="true"
        />
    </div>
</div>
