<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.create') @lang('site.dashboard.users')
        </h1>

        <form action="{{ route('dashboard.users.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow-md"
            enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.name')</label>
                <input type="text" name="name" value="{{ old('name') }}"
                    class="w-full border border-gray-300 rounded p-2">
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.email')</label>
                <input type="text" name="email" value="{{ old('email') }}"
                    class="w-full border border-gray-300 rounded p-2">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.password')</label>
                <input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}"
                    class="w-full border border-gray-300 rounded p-2">
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Password Confirmation</label>
                <input type="password" name="password" value="{{ old('password') }}"
                    class="w-full border border-gray-300 rounded p-2">
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.contact_number')</label>
                <input type="text" name="contact_number" value="{{ old('contact_number') }}"
                    class="w-full border border-gray-300 rounded p-2">
                @error('contact_number')
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
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="active" value="1" class="mr-2">
                    @lang('site.active')
                </label>
                @error('active')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>
            <!-- Role -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.role')</label>
                <select name="role_id" class="w-full border border-gray-300 rounded p-2">

                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                @error('role_id')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
                    @lang('site.create')
                </button>
        </form>
    </div>
</x-app-layout>
