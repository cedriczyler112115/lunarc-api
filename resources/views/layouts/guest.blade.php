<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

        <!-- Dark Mode: Apply before CSS loads to prevent flash -->
        <script>
            (function() {
                var theme = localStorage.getItem('theme');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-950 px-4 sm:px-0 transition-colors duration-300">
            <div class="mb-2">
                <a href="/" class="group block">
                    <img src="{{ asset('images/logo-full.png') }}" alt="{{ config('app.name', 'OneDrive') }}"
                        class="w-48 sm:w-56 h-auto object-contain drop-shadow-sm dark:drop-shadow-[0_4px_16px_rgba(16,185,129,0.2)] dark:brightness-110 group-hover:scale-105 transition-all duration-300" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-4 sm:px-6 py-6 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800/80 shadow-xl overflow-hidden rounded-2xl sm:rounded-3xl transition-colors duration-300">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
