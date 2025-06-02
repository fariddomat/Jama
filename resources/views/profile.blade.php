<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('الملف الشخصي') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-profile-information-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

        </div>
    </div>

    <style>
        /* RTL Support */
        html[dir="rtl"] .max-w-7xl {
            text-align: right;
        }

        html[dir="rtl"] .space-y-6 > div {
            text-align: right;
        }

        /* Custom Button Styling (if Livewire forms use buttons) */
        .profile-section button[type="submit"] {
            background-color: #25b2d9; /* Theme blue */
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .profile-section button[type="submit"]:hover {
            background-color: #e5690d; /* Darker blue */
        }

        /* Responsive Adjustments */
        @media (max-width: 640px) {
            .profile-section {
                padding: 1rem;
            }
        }
    </style>
</x-app-layout>
