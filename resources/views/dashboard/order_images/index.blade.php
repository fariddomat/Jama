<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">OrderImage</h1>
        <a href="{{ route('dashboard.order_images.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded shadow" wire:navigate>➕ @lang('site.add') OrderImage</a>

        <div class="overflow-x-auto mt-4">
            <x-autocrud::table
                :columns="['id', 'order_id', 'path', 'type']"
                :data="$orderImages"
                routePrefix="dashboard.order_images"
                :show="true"
                :edit="true"
                :delete="true"
                :restore="true"
            />
        </div>
    </div>
</x-app-layout>