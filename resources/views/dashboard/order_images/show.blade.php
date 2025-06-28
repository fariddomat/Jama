<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.show') @lang('site.dashboard.order_images')
        </h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.order_id')</label>
                <p class="text-gray-900">
                    @isset($orderImage->order)
                        {{ $orderImage->order->name ?? '—' }}
                    @else
                        {{ $orderImage->order_id ?? '—' }}
                    @endisset
                </p>
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.path')</label>
                <p class="text-gray-900">{{ $orderImage->path ?? '—' }}</p>
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.type')</label>
                <p class="text-gray-900">{{ $orderImage->type ?? '—' }}</p>
            </div>
            <a href="{{ route('dashboard.order_images.index') }}" class="mt-4 inline-block px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-700">
                @lang('site.back')
            </a>
        </div>
    </div>
</x-app-layout>