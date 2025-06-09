<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ClicknEat') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100">
            <div class="mb-8">
                <a href="/" class="flex items-center space-x-2">
                    <x-application-logo class="w-16 h-16 fill-current text-indigo-600" />
                    <span class="text-2xl font-bold text-indigo-600">ClicknEat</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white shadow-xl overflow-hidden sm:rounded-xl">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} ClicknEat. Tous droits réservés.</p>
            </div>
        </div>
    </body>
</html>
