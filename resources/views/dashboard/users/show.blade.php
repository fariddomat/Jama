<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">@lang('site.show') User</h1>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">@lang('site.name')</label>
                    <p class="text-gray-900">{{ $user->name ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">@lang('site.email')</label>
                    <p class="text-gray-900">{{ $user->email ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">@lang('site.contact_number')</label>
                    <p class="text-gray-900">{{ $user->contact_number ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">@lang('site.address')</label>
                    <p class="text-gray-900">{!! $user->address ?? '—' !!}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">@lang('site.active')</label>
                    <p class="text-gray-900">{{ $user->active ? 'Yes' : 'No' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">@lang('site.role')</label>
                    <p class="text-gray-900">{{ $user->roles->pluck('name')->implode(', ') ?? '—' }}</p>
                </div>
            </div>

            @if ($user->hasRole('merchant'))
                <h2 class="text-xl font-semibold mb-4">merchant orders</h2>
                <x-table

                :columns="['id', 'customer_id', 'merchant_id', 'delivery_agent_id', 'status']"
                    :data="$user->merchantOrders"
                    routePrefix="dashboard.orders"
                    :show="true"
                    :edit="true"
                    :delete="true"
                    :restore="true"
                />
            @endif

            @if ($user->hasRole('delivery_agent'))
                <h2 class="text-xl font-semibold mb-4">assigned orders</h2>
                <x-table
                :columns="['id', 'customer_id', 'merchant_id', 'delivery_agent_id', 'status']"
                    :data="$user->assignedOrders"
                    routePrefix="dashboard.orders"
                    :show="true"
                    :edit="true"
                    :delete="true"
                    :restore="true"
                />
            @endif

            <a href="{{ route('dashboard.users.index') }}" class="mt-6 inline-block px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-700">
                @lang('site.back')
            </a>
        </div>
    </div>
</x-app-layout>
