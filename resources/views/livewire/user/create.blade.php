<div class="container mx-auto p-6">

    <h1 class="text-2xl font-bold mb-4">
        @lang('site.create') @lang('site.user')
    </h1>
    @if (session()->has('message'))
        <div class="alert alert-success" id="success-message">
            {{ session('message') }}
        </div>
    @endif
    <form wire:submit.prevent="save" class="bg-white p-6 rounded-lg shadow-md">
        <!-- Name -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">@lang('site.name')</label>
            <input type="text" wire:model="name" class="w-full border border-gray-300 rounded p-2">
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">@lang('site.email')</label>
            <input type="email" wire:model="email" class="w-full border border-gray-300 rounded p-2">
            @error('email')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">@lang('site.password')</label>
            <input type="password" wire:model="password" class="w-full border border-gray-300 rounded p-2">
            @error('password')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Role -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">@lang('site.role')</label>
            <select wire:model="role_id" class="w-full border border-gray-300 rounded p-2">
                <option value="">Select Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
            @error('role_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded shadow hover:bg-blue-700">
            @lang('site.create') User
        </button>
    </form>


</div>
