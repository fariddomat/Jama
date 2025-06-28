<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * التعامل مع طلب تسجيل الدخول الوارد.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: RouteServiceProvider::HOME, navigate: true);
    }
}; ?>

<div class="login-container">
    <!-- حالة الجلسة -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="login-card">
        <h2 class="login-title text-white">{{ __('Login') }}</h2>
        <form wire:submit="login" class="space-y-6">
            <!-- البريد الإلكتروني أو رقم الهاتف -->
            <div>
                <x-input-label for="login" :value="__('Email or Phone Number')" />
                <x-text-input wire:model="form.login" id="login" class="block mt-1 w-full" type="text" name="login" required autofocus autocomplete="username" dir="ltr" />
                <x-input-error :messages="$errors->get('form.login')" class="mt-2" />
            </div>

            <!-- كلمة المرور -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input wire:model="form.password" id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" dir="ltr" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
            </div>

            <!-- تذكرني -->
            <div class="block mt-4">
                <label for="remember" class="inline-flex items-center">
                    <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                    <span class="mr-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot password?') }}
                    </a>
                @endif

                <x-primary-button class="bg-blue-500 hover:bg-blue-600">
                    {{ __('Login') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
