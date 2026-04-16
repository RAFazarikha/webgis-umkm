<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name'))</title>

        <meta name="title" content="@yield('title', config('app.name', 'Peta Kuliner Sumenep'))">
        <meta name="description" content="@yield('meta_description', 'Temukan berbagai destinasi dan clustering kuliner terbaik di Kabupaten Sumenep melalui sistem pemetaan spasial kami.')">

        <meta property="og:type" content="@yield('meta_type', 'website')">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="@yield('title', config('app.name', 'Peta Kuliner Sumenep'))">
        <meta property="og:description" content="@yield('meta_description', 'Temukan berbagai destinasi dan clustering kuliner terbaik di Kabupaten Sumenep melalui sistem pemetaan spasial kami.')">
        <meta property="og:image" content="@yield('meta_image', asset('images/hero-kuliner.webp'))">

        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="@yield('title', config('app.name', 'Peta Kuliner Sumenep'))">
        <meta property="twitter:description" content="@yield('meta_description', 'Temukan berbagai destinasi dan clustering kuliner terbaik di Kabupaten Sumenep melalui sistem pemetaan spasial kami.')">
        <meta property="twitter:image" content="@yield('meta_image', asset('images/hero-kuliner.webp'))">

        <link rel="canonical" href="{{ url()->current() }}">

        @stack('meta_tags')

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
