<div class="container mx-auto p-6" style="max-width: 98%">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Total Orders -->
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Total Orders</h2>
            <p class="text-3xl font-bold text-blue-600">{{ $totalStats['orders']['total'] ?? 0 }}</p>
            <a href="{{ route('dashboard.orders.index') }}" class="text-sm text-blue-500 hover:underline" wire:navigate>View Orders</a>
        </div>

        <!-- Total Customers (Not for Delivery Agents) -->
        @if (!auth()->user()->hasRole('delivery_agent'))
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">Total Customers</h2>
                <p class="text-3xl font-bold text-blue-600">{{ $totalStats['customers'] ?? 0 }}</p>
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
                <li>Orders: <a href="{{ route('dashboard.orders.index') }}" class="text-blue-500 hover:underline" wire:navigate>{{ $lastWeekStats['orders']['total'] ?? 0 }}</a></li>
                <li>Pending: {{ $lastWeekStats['orders']['by_status']['Pending'] ?? 0 }}</li>
                <li>Out for Delivery: {{ $lastWeekStats['orders']['by_status']['Out for Delivery'] ?? 0 }}</li>
                <li>Delivered: {{ $lastWeekStats['orders']['by_status']['Delivered'] ?? 0 }}</li>
                <li>Not Delivered: {{ $lastWeekStats['orders']['by_status']['Not Delivered'] ?? 0 }}</li>
                <li>Returned: {{ $lastWeekStats['orders']['by_status']['Returned'] ?? 0 }}</li>
                <li>Unknown: {{ $lastWeekStats['orders']['by_status']['Unknown'] ?? 0 }}</li>
                @if (!auth()->user()->hasRole('delivery_agent'))
                    <li>Customers: {{ $lastWeekStats['customers'] ?? 0 }}</li>
                @endif
            </ul>
        </div>

        <!-- Last Month -->
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Last Month</h2>
            <ul class="mt-4 space-y-2">
                <li>Orders: <a href="{{ route('dashboard.orders.index') }}" class="text-blue-500 hover:underline" wire:navigate>{{ $lastMonthStats['orders']['total'] ?? 0 }}</a></li>
                <li>Pending: {{ $lastMonthStats['orders']['by_status']['Pending'] ?? 0 }}</li>
                <li>Out for Delivery: {{ $lastMonthStats['orders']['by_status']['Out for Delivery'] ?? 0 }}</li>
                <li>Delivered: {{ $lastMonthStats['orders']['by_status']['Delivered'] ?? 0 }}</li>
                <li>Not Delivered: {{ $lastMonthStats['orders']['by_status']['Not Delivered'] ?? 0 }}</li>
                <li>Returned: {{ $lastMonthStats['orders']['by_status']['Returned'] ?? 0 }}</li>
                <li>Unknown: {{ $lastMonthStats['orders']['by_status']['Unknown'] ?? 0 }}</li>
                @if (!auth()->user()->hasRole('delivery_agent'))
                    <li>Customers: {{ $lastMonthStats['customers'] ?? 0 }}</li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Orders by Status Chart -->
    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-700">Orders by Status</h2>
        <canvas id="ordersChart" class="mt-4"></canvas>
        <script>
            document.addEventListener('livewire:navigated', function () {
                const ctx = document.getElementById('ordersChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Pending', 'Out for Delivery', 'Delivered', 'Not Delivered', 'Returned', 'Unknown'],
                        datasets: [
                            {
                                label: 'Last Week',
                                data: [
                                    {{ $lastWeekStats['orders']['by_status']['Pending'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Out for Delivery'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Delivered'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Not Delivered'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Returned'] ?? 0 }},
                                    {{ $lastWeekStats['orders']['by_status']['Unknown'] ?? 0 }},
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
                                    {{ $lastMonthStats['orders']['by_status']['Unknown'] ?? 0 }},
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
</div>
