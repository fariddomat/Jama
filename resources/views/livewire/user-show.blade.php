<div class="container mx-auto p-6" style="max-width: 98%">
    <h1 class="text-2xl font-bold mb-4">Show User</h1>
    <div class="flex justify-between mb-4">
        <a href="{{ route('dashboard.users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-700">
            Back
        </a>
        @if (auth()->user()->hasRole('superadministrator'))
            <button wire:click="export" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
                Export
            </button>
        @endif
    </div>

    <!-- User Details -->
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">User Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <p class="text-gray-900">{{ $user->name ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="text-gray-900">{{ $user->email ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                <p class="text-gray-900">{{ $user->contact_number ?? '—' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <p class="text-gray-900">{!! $user->address ?? '—' !!}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Active</label>
                <p class="text-gray-900">{{ $user->active ? 'Yes' : 'No' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <p class="text-gray-900">{{ $user->roles->pluck('name')->implode(', ') ?? '—' }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Total Orders -->
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Total Orders</h2>
            <p class="text-3xl font-bold text-blue-600">{{ $totalStats['orders']['total'] }}</p>
            <a href="{{ route('dashboard.orders.index') }}" class="text-sm text-blue-500 hover:underline" wire:navigate>View Orders</a>
        </div>

        <!-- Total Customers (Not for Delivery Agents) -->
        @if (!$user->hasRole('delivery_agent'))
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">Total Customers</h2>
                <p class="text-3xl font-bold text-blue-600">{{ $totalStats['customers'] }}</p>
                <a href="{{ route('dashboard.customers.index') }}" class="text-sm text-blue-500 hover:underline" wire:navigate>View Customers</a>
            </div>
        @endif
    </div>

    <!-- Time-Based Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Last Week -->
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Last Week</h2>
            <ul class="mt-4 space-y-2">
                <li>Orders: <a href="{{ route('dashboard.orders.index') }}" class="text-blue-500 hover:underline" wire:navigate>{{ $lastWeekStats['orders']['total'] }}</a></li>
                <li>Pending: {{ $lastWeekStats['orders']['by_status']['Pending'] }}</li>
                <li>Out for Delivery: {{ $lastWeekStats['orders']['by_status']['Out for Delivery'] }}</li>
                <li>Delivered: {{ $lastWeekStats['orders']['by_status']['Delivered'] }}</li>
                <li>Not Delivered: {{ $lastWeekStats['orders']['by_status']['Not Delivered'] }}</li>
                <li>Returned: {{ $lastWeekStats['orders']['by_status']['Returned'] }}</li>
                @if (!$user->hasRole('delivery_agent'))
                    <li>Customers: {{ $lastWeekStats['customers'] }}</li>
                @endif
            </ul>
        </div>

        <!-- Last Month -->
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Last Month</h2>
            <ul class="mt-4 space-y-2">
                <li>Orders: <a href="{{ route('dashboard.orders.index') }}" class="text-blue-500 hover:underline" wire:navigate>{{ $lastMonthStats['orders']['total'] }}</a></li>
                <li>Pending: {{ $lastMonthStats['orders']['by_status']['Pending'] }}</li>
                <li>Out for Delivery: {{ $lastMonthStats['orders']['by_status']['Out for Delivery'] }}</li>
                <li>Delivered: {{ $lastMonthStats['orders']['by_status']['Delivered'] }}</li>
                <li>Not Delivered: {{ $lastMonthStats['orders']['by_status']['Not Delivered'] }}</li>
                <li>Returned: {{ $lastMonthStats['orders']['by_status']['Returned'] }}</li>
                @if (!$user->hasRole('delivery_agent'))
                    <li>Customers: {{ $lastMonthStats['customers'] }}</li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Orders by Status Chart -->
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
        <h2 class="text-lg font-semibold text-gray-700">Orders by Status</h2>
        <canvas id="ordersChart" class="mt-4"></canvas>
        <script>
            document.addEventListener('livewire:navigated', function () {
                const ctx = document.getElementById('ordersChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Pending', 'Out for Delivery', 'Delivered', 'Not Delivered', 'Returned'],
                        datasets: [
                            {
                                label: 'Last Week',
                                data: [
                                    {{ $lastWeekStats['orders']['by_status']['Pending'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Out for Delivery'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Delivered'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Not Delivered'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Returned'] ?? 0 }},
                                ],
                                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            },
                            {
                                label: 'Last Month',
                                data: [
                                    {{ $lastMonthStats['orders']['by_status']['Pending'] ?? 0 }},
                                    {{ $lastMonthStats['orders']['by_status']['Out for Delivery'] ?? 0 }},
                                    {{ $lastMonthStats['orders']['by_status']['Delivered'] ?? 0 }},
                                    {{ $lastMonthStats['orders']['by_status']['Not Delivered'] ?? 0 }},
                                    {{ $lastMonthStats['orders']['by_status']['Returned'] ?? 0 }},
                                ],
                                backgroundColor: 'rgba(16, 185, 129, 0.5)',
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { beginAtZero: true },
                        },
                    },
                });
            });
        </script>
    </div>

    <!-- Orders Tables -->
    @if ($user->hasRole('merchant'))
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Merchant Orders</h2>
            <x-table
                :columns="['id', 'customer_id', 'merchant_id', 'delivery_agent_id',  'delivery_time', 'otp', 'statuss']"
                :data="$user->merchantOrders"
                routePrefix="dashboard.orders"
                :show="true"
                :edit="true"
                :delete="true"
                :restore="true"
            />
        </div>
    @endif

    @if ($user->hasRole('delivery_agent'))
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Assigned Orders</h2>
            <x-table
                :columns="['id', 'customer_id', 'merchant_id', 'delivery_agent_id','delivery_time', 'otp', 'statuss']"
                :data="$user->assignedOrders"
                routePrefix="dashboard.orders"
                :show="true"
                :edit="true"
                :delete="true"
                :restore="true"
            />
        </div>
    @endif
</div>
