<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.edit') @lang('site.dashboard.items')
        </h1>

        <form action="{{ route('dashboard.items.update', $item->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" enctype="multipart/form-data">
            @csrf
            @method('PUT')
                        <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.order_id')</label>
                <select name="order_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">@lang('site.select_order_id')</option>
                    @foreach ($orders as $option)
                        <option value="{{ $option->id }}" {{ $item->order_id == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('order_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.name')</label>
                <input type="text" name="name" value="{{ old('name', $item->name) }}" class="w-full border border-gray-300 rounded p-2">
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.barcode')</label>
                <input type="text" name="barcode" value="{{ old('barcode', $item->barcode) }}" class="w-full border border-gray-300 rounded p-2">
                @error('barcode')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.status_id')</label>
                <select name="status_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">@lang('site.select_status_id')</option>
                    @foreach ($statuses as $option)
                        <option value="{{ $option->id }}" {{ $item->status_id == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('status_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
                @lang('site.update')
            </button>
        </form>
    </div>
</x-app-layout>