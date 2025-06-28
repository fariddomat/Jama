<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.create') @lang('site.dashboard.customers')
        </h1>

        <form action="{{ route('dashboard.customers.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.name')</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded p-2">
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.mobile')</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}" class="w-full border border-gray-300 rounded p-2">
                @error('mobile')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block  text-sm font-medium text-gray-700">@lang('site.mr_number')</label>
                <input type="text" name="mr_number" value="{{ old('mr_number') }}" class="w-full border border-gray-300 rounded p-2">
                @error('mr_number')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.address')</label>
                <textarea name="address" class="w-full border border-gray-300 rounded p-2">{{ old('address') }}</textarea>
                @error('address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
                @lang('site.create')
            </button>
        </form>
    </div>
</x-app-layout>
