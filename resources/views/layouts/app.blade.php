<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    livewire:navigate>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    {{-- <link rel="icon" type="image/x-icon" href=""> --}}

    <title>Logistic System | Jama</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('noty/noty.css') }}">
    <script src="{{ asset('noty/noty.min.js') }}" defer></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        html[dir="rtl"] th {
            text-align: right;
        }

        @media (max-width: 768px) {
            html[dir="rtl"] .main-b {
                margin-left: 0 !important;
                margin-right: -16rem !important;
            }

            html[dir="rtl"] .-translate-x-64 {
                --tw-translate-x: +16rem;
            }

            html[dir="ltr"] .main-b {
                margin-right: 0 !important;
                margin-left: -16rem !important;
            }

            html[dir="ltr"] .-translate-x-64 {
                --tw-translate-x: -16rem;
            }
        }

        @media (min-width: 768px) {
            html[dir="rtl"] aside {
                left: unset !important;
            }

            html[dir="ltr"] .main-b {
                margin-left: 16rem !important;
                margin-right: 0 !important;
            }

            html[dir="rtl"] .main-b {
                margin-left: 0 !important;
                margin-right: 16rem !important;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-x-hidden" x-data="{ open: false }">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden" x-show="open" @click="open = false"></div>

        <!-- Sidebar -->
        <aside
            class="bg-gray-900 text-white p-5 w-64 md:w-64 md:fixed md:top-0 md:left-0 md:h-screen transition-all duration-300 ease-in-out z-50 overflow-y-auto"
            :class="open ? 'translate-x-0' : '-translate-x-64 md:translate-x-0'">
            <h2 class="text-xl font-bold mb-4">@lang('site.control_panel')</h2>

            <nav class="mt-5 space-y-3">
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    @lang('site.home') <i class="fas fa-home"></i>
                </x-responsive-nav-link>

                <!-- Users (Superadministrator Only) -->
                @if (auth()->user()->hasRole('superadministrator'))
                    <x-responsive-nav-link href="{{ route('dashboard.users.index') }}" :active="Str::startsWith(request()->route()->getName(), 'dashboard.users.')">
                        @lang('site.users') <i class="fas fa-users"></i>
                    </x-responsive-nav-link>
                @endif

                <!-- Customers (Superadministrator or users with view-customers permission) -->
                @if (auth()->user()->hasRole('superadministrator') || auth()->user()->hasRole('merchant'))
                    <x-responsive-nav-link href="{{ route('dashboard.customers.index') }}" :active="Str::startsWith(request()->route()->getName(), 'dashboard.customers.')">
                        @lang('site.customers') <i class="fas fa-user-friends"></i>
                    </x-responsive-nav-link>
                @endif

                <!-- Statuses (Superadministrator or users with view-statuses permission) -->
                @if (auth()->user()->hasRole('superadministrator'))
                    <x-responsive-nav-link href="{{ route('dashboard.statuses.index') }}" :active="Str::startsWith(request()->route()->getName(), 'dashboard.statuses.')">
                        @lang('site.statuses') <i class="fas fa-list"></i>
                    </x-responsive-nav-link>
                @endif

                @if (auth()->user()->hasRole('delivery_agent'))
                    <x-responsive-nav-link href="{{ route('dashboard.delivery-agent-dashboard') }}" :active="Str::startsWith(request()->route()->getName(), 'dashboard.orders.')">
                        Delivery <i class="fas fa-cart-plus"></i>
                    </x-responsive-nav-link>
                @endif
                {{-- Orders --}}
                <x-responsive-nav-link href="{{ route('dashboard.orders.index') }}" :active="Str::startsWith(request()->route()->getName(), 'dashboard.orders.')">
                    @lang('site.orders') <i class="fas fa-cart-plus"></i>
                </x-responsive-nav-link>
                <!-- Items (Superadministrator or users with view-items permission) -->
                <x-responsive-nav-link href="{{ route('dashboard.items.index') }}" :active="Str::startsWith(request()->route()->getName(), 'dashboard.items.')">
                    @lang('site.items') <i class="fas fa-box"></i>
                </x-responsive-nav-link>


                <!-- Profile -->
                <x-responsive-nav-link href="{{ route('profile') }}" :active="request()->routeIs('profile')">
                    @lang('site.profile') <i class="fas fa-user-cog"></i>
                </x-responsive-nav-link>
            </nav>
        </aside>

        <div class="main-b flex-1 flex flex-col -ml-64 md:ml-64">
            <!-- Navbar -->
            <header class="bg-blue-800 text-white shadow p-4 flex justify-between items-center">
                <!-- Mobile Menu Button -->
                <button @click="open = !open" class="md:hidden text-gray-700 text-2xl">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- User Actions -->
                <div class="flex space-x-3 items-center text-lg">
                    <span class="cursor-pointer hover:text-red-500 pr-2 w-full">
                        <a class="w-full text-start" href="{{ route('dashboard.logout') }}" wire:navigate>
                            <i class="fas fa-sign-out-alt"></i> @lang('site.logout')
                        </a>
                    </span>
                </div>
            </header>

            <!-- Main Content -->
            <main class="p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include ajaxForm library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>


    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @extends('layouts._noty')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('redirect', (url) => {
                window.location.href = url;
            });
        });
    </script>

    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $("textarea").each(function(index) {
                // Skip if the textarea is invalid or hidden
                if (!this || $(this).is(':hidden') || $(this).parents().is(':hidden')) {
                    return true;
                }

                // Assign a unique ID if none exists
                if (!this.id) {
                    this.id = 'textarea-' + index + '-' + Math.random().toString(36).substr(2, 9);
                }

                // Initialize CKEditor only if not already initialized
                if (!CKEDITOR.instances[this.id]) {
                    CKEDITOR.replace(this.id, {
                        removeButtons: "About",
                        contentsLangDirection: $(this).attr('dir') || 'ltr'
                    });
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>

</body>

</html>
