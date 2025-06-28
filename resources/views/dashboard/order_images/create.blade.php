<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.create') @lang('site.dashboard.order_images')
        </h1>

        <form action="{{ route('dashboard.order_images.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" enctype="multipart/form-data">
            @csrf
                        <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.order_id')</label>
                <select name="order_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">@lang('site.select_order_id')</option>
                    @foreach ($orders as $option)
                        <option value="{{ $option->id }}" {{ old('order_id') == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('order_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.path')</label>
                <input type="text" name="path" value="{{ old('path') }}" class="w-full border border-gray-300 rounded p-2">
                @error('path')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.type')</label>
                <input type="text" name="type" value="{{ old('type') }}" class="w-full border border-gray-300 rounded p-2">
                @error('type')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
                @lang('site.create')
            </button>
        </form>
    </div>
</x-app-layout>