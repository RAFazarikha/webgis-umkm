<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name'))</title>

        <!-- Logo -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logos/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logos/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logos/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('logos/site.webmanifest') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/alpinejs" defer></script>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-gray-50 text-gray-900 antialiased flex flex-col min-h-screen">

        <x-navbar />

        <main class="flex-1 w-full">
            @yield('content')
        </main>

        <x-footer />

    </body>
</html>
