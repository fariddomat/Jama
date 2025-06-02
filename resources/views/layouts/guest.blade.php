<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'لارافيل') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <style>
        /* Guest Container */
        .guest-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #173c4d;
            /* Light gray */
            padding: 1rem;
        }

        /* Guest Card */
        .guest-card {
            background-color: #ffffff6b;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        /* Guest Title */
        .guest-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1f2937;
            /* Dark gray */
            margin-bottom: 1.5rem;
        }

        /* Form Inputs */
        .guest-card input[type="email"],
        .guest-card input[type="password"] {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1rem;
            width: 100%;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .guest-card input:focus {
            border-color: #25b2d9;
            /* Theme blue */
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
            outline: none;
        }

        /* Checkbox */
        .guest-card input[type="checkbox"] {
            accent-color: #25b2d9;
            /* Theme blue */
        }

        /* Links */
        .guest-card a {
            color: #6b7280;
            /* Gray */
            transition: color 0.3s ease;
        }

        .guest-card a:hover {
            color: #25b2d9;
            /* Theme blue */
        }

        /* Logo */
        .guest-logo {
            margin-bottom: 1.5rem;
        }

        /* RTL Support */
        html[dir="rtl"] .guest-card {
            text-align: center;
        }

        html[dir="rtl"] .inline-flex label span {
            margin-right: 0;
            margin-left: 0.5rem;
        }


        /* Responsive Design */
        @media (max-width: 640px) {
            .guest-card {
                padding: 1.5rem;
            }

            .guest-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body class="antialiased">
    <div class="guest-container">
        <div class="guest-card">
            <div class="guest-logo">
                <a href="/" wire:navigate style="text-align: -webkit-center;">
                    <img src="{{ asset('logo/Al-Hussam 4.png') }}" class="w-32 h-40 fill-current text-gray-500"
                        alt="">
                </a>
            </div>
            {{ $slot }}
        </div>
    </div>
    @livewireScripts
</body>

</html>
