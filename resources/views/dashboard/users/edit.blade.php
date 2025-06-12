<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">
            @lang('site.edit') @lang('site.dashboard.users')
        </h1>

        <form action="{{ route('dashboard.users.update', $user->id) }}" method="POST" class="bg-white p-6 rounded-lg shadow-md" enctype="multipart/form-data">
            @csrf
            @method('PUT')
                        <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.name')</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded p-2">
                @error('name')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.email')</label>
                <input type="text" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded p-2">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.contact_number')</label>
                <input type="text" name="contact_number" value="{{ old('contact_number', $user->contact_number) }}" class="w-full border border-gray-300 rounded p-2">
                @error('contact_number')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">@lang('site.address')</label>
                <textarea name="address" class="w-full border border-gray-300 rounded p-2">{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="active" value="1" class="mr-2" {{ $user->active ? 'checked' : '' }}>
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
                    <option value="{{ $role->id }}" @if ($user->roles->first()->id ==$role->id)
                    selected                       
                    @endif>{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
            @error('role_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
                @lang('site.update')
            </button>
        </form>
    </div>
</x-app-layout>