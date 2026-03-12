<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logos/favicon-32x32.png') }}" alt="Logo" class="" />
            <span class="text-xl font-semibold text-[#111827]">Jelajah Rasa</span>
        </div>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="/" class="{{ Route::is('home') ? 'text-[#D92D20]' : 'hover:text-[#D92D20]' }}">Beranda</a>
            <a href="/map" class="{{ Route::is('map') ? 'text-[#D92D20]' : 'hover:text-[#D92D20]' }}">Map</a>
            <a href="/kuliner" class="{{ Route::is('kuliner') ? 'text-[#D92D20]' : 'hover:text-[#D92D20]' }}">Kuliner</a>
            <a href="/tentang" class="{{ Route::is('tentang') ? 'text-[#D92D20]' : 'hover:text-[#D92D20]' }}">Tentang</a>
            @auth
                <a href="/admin/dashboard" class="hover:text-[#D92D20]">Dashboard</a>
            @endauth
        </div>
        <div class="hidden md:block">
            <form method="GET" action="/map" class="">
                <input type="text"
                    name="search"
                    placeholder="Cari UMKM..."
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#F59E0B]">

                <button
                    class="px-6 py-2 bg-[#D92D20] text-white rounded-lg ms-2">
                    Search
                </button>
            </form>
        </div>
    </div>
</nav>
