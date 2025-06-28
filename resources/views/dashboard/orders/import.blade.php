<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.import') @lang('site.dashboard.orders')
        </h1>

        <form action="{{ route('dashboard.orders.import.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.excel_file')</label>
                <input type="file" name="file" class="w-full border border-gray-300 rounded p-2" accept=".xlsx,.xls">
                @error('file')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
                @lang('site.import')
            </button>
        </form>
    </div>
</x-app-layout>
