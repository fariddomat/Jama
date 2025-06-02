<div class="">
    <input type="text" wire:model.lazy="search"  wire:keydown="handleSearch" placeholder="Search users..."
    class="w-full p-2 mb-2 border border-gray-300 rounded shadow">


    <x-table
        :columns="['name', 'email', 'created_at']"
        :data="$users"
        :routePrefix="'dashboard.users'"
        :show="true"
        :edit="true"
        :delete="true"
    />
</div>
