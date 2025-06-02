<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * إرسال رابط إعادة تعيين كلمة المرور إلى عنوان البريد الإلكتروني المقدم.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // سنرسل رابط إعادة تعيين كلمة المرور إلى هذا المستخدم. بمجرد محاولة إرسال الرابط،
        // سنفحص الاستجابة ثم نحدد الرسالة التي يجب عرضها للمستخدم. أخيرًا، سنرسل استجابة مناسبة.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div dir="rtl">
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('نسيت كلمة المرور؟ لا مشكلة. فقط أخبرنا بعنوان بريدك الإلكتروني وسنرسل لك رابط إعادة تعيين كلمة المرور الذي سيسمح لك باختيار كلمة مرور جديدة.') }}
    </div>

    <!-- حالة الجلسة -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink">
        <!-- عنوان البريد الإلكتروني -->
        <div>
            <x-input-label for="email" :value="__('البريد الإلكتروني')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-start mt-4">
            <x-primary-button>
                {{ __('إرسال رابط إعادة تعيين كلمة المرور') }}
            </x-primary-button>
        </div>
    </form>
</div>
