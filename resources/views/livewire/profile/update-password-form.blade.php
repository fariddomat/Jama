<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * تحديث كلمة المرور للمستخدم المصادق عليه حاليًا.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="password-section">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('تحديث كلمة المرور') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمنًا.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <x-input-label for="update_password_current_password" :value="__('كلمة المرور الحالية')" />
            <x-text-input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('كلمة المرور الجديدة')" />
            <x-text-input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('تأكيد كلمة المرور')" />
            <x-text-input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-blue-500 hover:bg-blue-600">
                {{ __('حفظ') }}
            </x-primary-button>

            <x-action-message class="ml-3" on="password-updated">
                {{ __('تم الحفظ.') }}
            </x-action-message>
        </div>
    </form>
<style>
    /* RTL Support */
    html[dir="rtl"] .password-section {
        text-align: right;
    }

    html[dir="rtl"] .flex.items-center.gap-4 {
        flex-direction: row-reverse;
    }

    /* Input Styling */
    .password-section input[type="password"] {
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0.75rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .password-section input:focus {
        border-color: #25b2d9; /* Theme blue */
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
        outline: none;
    }

    /* Responsive Adjustments */
    @media (max-width: 640px) {
        .password-section {
            padding: 1rem;
        }
    }
</style>
</section>

