<nav x-data="{ open: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

        <!-- Logo -->
        <div class="flex items-center gap-3">
            <a href="/" class="flex items-center gap-3">
                <img src="{{ asset('logos/favicon-32x32.png') }}" alt="Logo" />
                <span class="text-xl font-semibold text-[#111827]">Jelajah Rasa</span>
            </a>
        </div>

        <!-- Menu Desktop -->
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="/" class="{{ Route::is('home') ? 'text-[#D92D20]' : 'hover:text-[#D92D20]' }}">Beranda</a>
            <a href="/map" class="{{ Route::is('map') ? 'text-[#D92D20]' : 'hover:text-[#D92D20]' }}">Map</a>
            <a href="/kuliner" class="{{ Route::is('kuliner') ? 'text-[#D92D20]' : 'hover:text-[#D92D20]' }}">Kuliner</a>
            <a href="/tentang" class="{{ Route::is('tentang') ? 'text-[#D92D20]' : 'hover:text-[#D92D20]' }}">Tentang</a>
            @auth
                <a href="/admin/dashboard" class="hover:text-[#D92D20]">Dashboard</a>
            @endauth
        </div>

        <!-- Search Desktop -->
        <div class="hidden md:block">
            <form method="GET" action="/map">
                <input type="text" name="search" placeholder="Cari UMKM..."
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F59E0B]">
                <button class="px-6 py-2 bg-[#D92D20] text-white rounded-lg ms-2">
                    Search
                </button>
            </form>
        </div>

        <!-- Hamburger Button -->
        <button @click="open = !open" class="md:hidden focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div x-show="open" x-transition class="md:hidden px-6 pb-4 space-y-3 bg-white border-t">

        <a href="/" class="block py-2">Beranda</a>
        <a href="/map" class="block py-2">Map</a>
        <a href="/kuliner" class="block py-2">Kuliner</a>
        <a href="/tentang" class="block py-2">Tentang</a>

        @auth
            <a href="/admin/dashboard" class="block py-2">Dashboard</a>
        @endauth

        <!-- Search Mobile -->
        <form method="GET" action="/map" class="pt-2">
            <input type="text" name="search" placeholder="Cari UMKM..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F59E0B]">
            <button class="w-full mt-2 px-4 py-2 bg-[#D92D20] text-white rounded-lg">
                Search
            </button>
        </form>
    </div>
</nav>
