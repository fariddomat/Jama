<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.show') @lang('site.dashboard.orders')
        </h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.customer_id')</label>
                <p class="text-gray-900">
                    @isset($order->customer)
                        {{ $order->customer->name ?? '—' }}
                    @else
                        {{ $order->customer_id ?? '—' }}
                    @endisset
                </p>
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.merchant_id')</label>
                <p class="text-gray-900">
                    @isset($order->merchant)
                        {{ $order->merchant->name ?? '—' }}
                    @else
                        {{ $order->merchant_id ?? '—' }}
                    @endisset
                </p>
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.delivery_agent_id')</label>
                <p class="text-gray-900">
                    @isset($order->deliveryAgent)
                        {{ $order->deliveryAgent->name ?? '—' }}
                    @else
                        {{ $order->delivery_agent_id ?? '—' }}
                    @endisset
                </p>
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.from_address')</label>
                <p class="text-gray-900">{{ $order->from_address ?? '—' }}</p>
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.to_address')</label>
                <p class="text-gray-900">{{ $order->to_address ?? '—' }}</p>
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.delivery_time')</label>
                <p class="text-gray-900">{{ $order->delivery_time ? $order->delivery_time->format('Y-m-d" . (datetime === 'datetime' ? ' H:i' : '') . "') : '—' }}</p>
            </div>
            <a href="{{ route('dashboard.orders.index') }}" class="mt-4 inline-block px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-700">
                @lang('site.back')
            </a>
        </div>
    </div>
</x-app-layout>