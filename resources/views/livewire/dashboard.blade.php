
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">{{ __('Dashboard') }}</h1>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Orders -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">{{ __('Total Orders') }}</h2>
                <p class="text-3xl font-bold text-blue-600">{{ $totalStats['orders']['total'] }}</p>
                <a href="{{ route('dashboard.orders.index') }}" class="text-sm text-blue-500 hover:underline" wire:navigate>{{ __('View Orders') }}</a>
            </div>

            <!-- Total Customers (Not for Delivery Agents) -->
            @if (!Auth::user()->hasRole('delivery_agent'))
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-700">{{ __('Total Customers') }}</h2>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalStats['customers'] }}</p>
                    <a href="{{ route('dashboard.customers.index') }}" class="text-sm text-blue-500 hover:underline" wire:navigate>{{ __('View Customers') }}</a>
                </div>
            @endif

            <!-- Total Items -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">{{ __('Total Items') }}</h2>
                <p class="text-3xl font-bold text-blue-600">{{ $totalStats['items']['total'] }}</p>
                <a href="{{ route('dashboard.items.index') }}" class="text-sm text-blue-500 hover:underline" wire:navigate>{{ __('View Items') }}</a>
            </div>
        </div>

        <!-- Time-Based Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Last Week -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">{{ __('Last Week') }}</h2>
                <ul class="mt-4 space-y-2">
                    <li>{{ __('Orders') }}: <a href="{{ route('dashboard.orders.index') }}" class="text-blue-500 hover:underline" wire:navigate>{{ $lastWeekStats['orders']['total'] }}</a></li>
                    <li>{{ __('Pending') }}: {{ $lastWeekStats['orders']['pending'] }}</li>
                    <li>{{ __('Out for Delivery') }}: {{ $lastWeekStats['orders']['out_for_delivery'] }}</li>
                    <li>{{ __('Delivered') }}: {{ $lastWeekStats['orders']['delivered'] }}</li>
                    <li>{{ __('Not Delivered') }}: {{ $lastWeekStats['orders']['not_delivered'] }}</li>
                    <li>{{ __('Returned') }}: {{ $lastWeekStats['orders']['returned'] }}</li>
                    @if (!Auth::user()->hasRole('delivery_agent'))
                        <li>{{ __('Customers') }}: {{ $lastWeekStats['customers'] }}</li>
                    @endif
                </ul>
            </div>

            <!-- Last Month -->
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">{{ __('Last Month') }}</h2>
                <ul class="mt-4 space-y-2">
                    <li>{{ __('Orders') }}: <a href="{{ route('dashboard.orders.index') }}" class="text-blue-500 hover:underline" wire:navigate>{{ $lastMonthStats['orders']['total'] }}</a></li>
                    <li>{{ __('Pending') }}: {{ $lastMonthStats['orders']['pending'] }}</li>
                    <li>{{ __('Out for Delivery') }}: {{ $lastMonthStats['orders']['out_for_delivery'] }}</li>
                    <li>{{ __('Delivered') }}: {{ $lastMonthStats['orders']['delivered'] }}</li>
                    <li>{{ __('Not Delivered') }}: {{ $lastMonthStats['orders']['not_delivered'] }}</li>
                    <li>{{ __('Returned') }}: {{ $lastMonthStats['orders']['returned'] }}</li>
                    @if (!Auth::user()->hasRole('delivery_agent'))
                        <li>{{ __('Customers') }}: {{ $lastMonthStats['customers'] }}</li>
                    @endif
                </ul>
            </div>
        </div>

        <!-- Items by Status Chart -->
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">{{ __('Items by Status') }}</h2>
            <canvas id="itemsChart" class="mt-4"></canvas>
            <script>
                document.addEventListener('livewire:navigated', function () {
                    const ctx = document.getElementById('itemsChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Pending', 'Out for Delivery', 'Delivered', 'Not Delivered', 'Returned'],
                            datasets: [
                                {
                                    label: '{{ __('Last Week') }}',
                                    data: [
                                        {{ $lastWeekStats['items']['by_status']['Pending'] ?? 0 }},
                                        {{ $lastWeekStats['items']['by_status']['Out for Delivery'] ?? 0 }},
                                        {{ $lastWeekStats['items']['by_status']['Delivered'] ?? 0 }},
                                        {{ $lastWeekStats['items']['by_status']['Not Delivered'] ?? 0 }},
                                        {{ $lastWeekStats['items']['by_status']['Returned'] ?? 0 }},
                                    ],
                                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                                },
                                {
                                    label: '{{ __('Last Month') }}',
                                    data: [
                                        {{ $lastMonthStats['items']['by_status']['Pending'] ?? 0 }},
                                        {{ $lastMonthStats['items']['by_status']['Out for Delivery'] ?? 0 }},
                                        {{ $lastMonthStats['items']['by_status']['Delivered'] ?? 0 }},
                                        {{ $lastMonthStats['items']['by_status']['Not Delivered'] ?? 0 }},
                                        {{ $lastMonthStats['items']['by_status']['Returned'] ?? 0 }},
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
