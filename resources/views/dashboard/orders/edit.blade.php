<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.edit') @lang('site.dashboard.orders')
        </h1>

        <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.customer_id')</label>
                <select name="customer_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">@lang('site.select_customer_id')</option>
                    @foreach ($customers as $option)
                        <option value="{{ $option->id }}" {{ $order->customer_id == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.merchant_id')</label>
                <select name="merchant_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">@lang('site.select_merchant_id')</option>
                    @foreach ($merchants as $option)
                        <option value="{{ $option->id }}" {{ $order->merchant_id == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('merchant_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.delivery_agent_id')</label>
                <select name="delivery_agent_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">@lang('site.select_delivery_agent_id')</option>
                    @foreach ($deliveryAgents as $option)
                        <option value="{{ $option->id }}" {{ $order->delivery_agent_id == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('delivery_agent_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.from_address')</label>
                <textarea name="from_address" class="w-full border border-gray-300 rounded p-2">{{ old('from_address', $order->from_address) }}</textarea>
                @error('from_address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.to_address')</label>
                <textarea name="to_address" class="w-full border border-gray-300 rounded p-2">{{ old('to_address', $order->to_address) }}</textarea>
                @error('to_address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.delivery_time')</label>
                <input type="datetime-local" name="delivery_time" value="{{ old('delivery_time', $order->delivery_time ? $order->delivery_time : '') }}" class="w-full border border-gray-300 rounded p-2">
                @error('delivery_time')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.notes')</label>
                <textarea name="notes" class="w-full border border-gray-300 rounded p-2">{{ old('notes', $order->notes) }}</textarea>
                @error('notes')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
                @lang('site.update')
            </button>
        </form>
    </div>
</x-app-layout>
