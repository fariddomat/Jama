<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">@lang('site.users')</h1>
    <div class="flex justify-between mb-4">
        <a href="{{ route('dashboard.users.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-600" wire:navigate>
            ➕ @lang('site.add') User
        </a>
        <a href="{{ route('dashboard.users.export') }}?search={{ $search }}&role={{ $role }}" class="px-4 py-2 bg-green-500 text-white rounded shadow hover:bg-green-600">
            📤 Export
        </a>
    </div>

    <div class="mb-4 flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <input wire:model.live="search" type="text" placeholder="@lang('site.search')..." class="border-gray-300 rounded-md shadow-sm p-2 w-full md:w-1/3">
        <select wire:model.live="role" class="border-gray-300 rounded-md shadow-sm p-2 w-full md:w-1/3">
            <option value="">All Roles</option>
            @foreach ($roles as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-x-auto">
        <x-table
            :columns="['id', 'name', 'email', 'contact_number', 'active']"
            :data="$users"
            routePrefix="dashboard.users"
            :show="true"
            :edit="true"
            :delete="true"
            :restore="true"
        />
    </div>
</div>
