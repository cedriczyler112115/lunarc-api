<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=5">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#10b981" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">

    <!-- Dark Mode & URL Clean: Apply before CSS loads to prevent flash -->
    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            if (window.location.search) {
                window.history.replaceState(null, '', window.location.pathname);
            }
        })();
    </script>

    <!-- jQuery & jQuery-Confirm -->
    <link rel="stylesheet" href="{{ asset('vendor/jquery-confirm/jquery-confirm.min.css') }}">
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <!-- Polyfill: $.trim was removed in jQuery 4, but jquery-confirm v3 needs it -->
    <script>if (window.jQuery && !jQuery.trim) { jQuery.trim = function(s) { return s == null ? '' : (s + '').replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, ''); }; }</script>
    <script src="{{ asset('vendor/jquery-confirm/jquery-confirm.min.js') }}"></script>

    <!-- Logout Confirmation & Dark Mode Toggle -->
    <script>
        window.confirmLogout = function(form) {
            if (window.jQuery && typeof window.jQuery.confirm === 'function') {
                window.jQuery.confirm({
                    title: 'Confirm Log Out',
                    content: 'Are you sure you want to log out of your account?',
                    type: 'red',
                    typeAnimated: true,
                    theme: 'modern',
                    animation: 'scale',
                    closeAnimation: 'scale',
                    backgroundDismiss: true,
                    buttons: {
                        logout: {
                            text: 'Log Out',
                            btnClass: 'btn-red',
                            action: function() {
                                form.submit();
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            btnClass: 'btn-default'
                        }
                    }
                });
            } else {
                if (confirm('Are you sure you want to log out?')) {
                    form.submit();
                }
            }
        };

        window.toggleDarkMode = function() {
            var html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        };

        window.isDarkMode = function() {
            return document.documentElement.classList.contains('dark');
        };
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-950 transition-colors duration-300">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-900 border-b border-gray-200/60 dark:border-gray-800 transition-colors duration-300">
                <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1 pb-24 sm:pb-0">
            {{ $slot }}
        </main>

        <!-- Desktop Car Rental Themed Footer -->
        @include('layouts.footer')
    </div>
</body>

</html>