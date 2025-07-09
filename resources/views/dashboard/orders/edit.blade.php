<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            Edit Order
        </h1>

        <form action="{{ route('dashboard.orders.update', $order->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Customer</label>
                <select name="customer_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $option)
                        <option value="{{ $option->id }}" {{ $order->customer_id == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Merchant</label>
                <select name="merchant_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">Select Merchant</option>
                    @foreach ($merchants as $option)
                        <option value="{{ $option->id }}" {{ $order->merchant_id == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('merchant_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Delivery Agent</label>
                <select name="delivery_agent_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">Select Delivery Agent</option>
                    @foreach ($deliveryAgents as $option)
                        <option value="{{ $option->id }}" {{ $order->delivery_agent_id == $option->id ? 'selected' : '' }}>{{ $option->name }}</option>
                    @endforeach
                </select>
                @error('delivery_agent_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status_id" class="w-full border border-gray-300 rounded p-2">
                    <option value="">Select Status</option>
                    @foreach ($statuses as $id => $name)
                        <option value="{{ $id }}" {{ $order->status_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('status_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">From Address</label>
                <textarea name="from_address" class="w-full border border-gray-300 rounded p-2">{{ old('from_address', $order->from_address) }}</textarea>
                @error('from_address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">To Address</label>
                <textarea name="to_address" class="w-full border border-gray-300 rounded p-2">{{ old('to_address', $order->to_address) }}</textarea>
                @error('to_address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Delivery Time</label>
                <input type="datetime-local" name="delivery_time" value="{{ old('delivery_time', $order->delivery_time ? $order->delivery_time : '') }}" class="w-full border border-gray-300 rounded p-2">
                @error('delivery_time')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" class="w-full border border-gray-300 rounded p-2">{{ old('notes', $order->notes) }}</textarea>
                @error('notes')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
                Update
            </button>
        </form>
    </div>
</x-app-layout>
