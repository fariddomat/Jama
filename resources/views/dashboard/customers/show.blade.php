<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.show') Customers
        </h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.name')</label>
                <p class="text-gray-900">{{ $customer->name ?? '—' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.mobile')</label>
                <p class="text-gray-900">{{ $customer->mobile ?? '—' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.mr_number')</label>
                <p class="text-gray-900">{{ $customer->mr_number ?? '—' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.address')</label>
                <p class="text-gray-900">{!! $customer->address ?? '—' !!}</p>
            </div>
            <a href="{{ route('dashboard.customers.index') }}" class="mt-4 inline-block px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-700">
                @lang('site.back')
            </a>
        </div>
    </div>
</x-app-layout>
