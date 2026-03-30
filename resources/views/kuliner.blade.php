@extends('layouts.app')
@section('title', 'Kuliner - Jelajah Rasa')
@section('content')

<section class="max-w-7xl mx-auto px-6 py-12 text-center items-center justify-center">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-[#111827] mb-4">Ragam Kuliner Sumenep</h1>
    </div>

    <!-- Search Section -->
    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm mb-12">
        <div class="grid md:grid-cols-3 gap-6 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Search</label>
                <div class="relative">
                    <input type="text" name="search" placeholder="Search for culinary spots..."
                        class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F59E0B] focus:border-[#F59E0B]" />
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-5 h-5 absolute right-4 top-1/2 -translate-y-1/2 text-[#111827]"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m1.6-5.4a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div>
                <button type="submit"
                    class="w-full px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-[#F59E0B] transition font-medium">
                    Search
                </button>
            </div>
        </div>
    </div>

    <!-- Culinary List -->
    <div class="space-y-6">
        @foreach ($umkms as $umkm)
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition flex flex-col md:flex-row gap-6 items-center justify-center text-center md:text-left md:items-start md:justify-start">
            <a href="{{ route('kuliner.view', $umkm->id) }}">
            <div class="w-full h-48 md:w-32 md:h-32 bg-gray-200 rounded-xl flex-shrink-0 overflow-hidden">
                @if ($umkm->kategori == 'makanan_khas')
                    <img class="w-full h-full object-cover rounded-xl" src="{{ asset('images/makanan-khas.webp') }}" alt="">
                @elseif ($umkm->kategori == 'makanan_berat')
                    <img class="w-full h-full object-cover rounded-xl" src="{{ asset('images/makanan-berat.webp') }}" alt="">
                @elseif ($umkm->kategori == 'minuman')
                    <img class="w-full h-full object-cover rounded-xl" src="{{ asset('images/minuman.webp') }}" alt="">
                @else
                <img class="w-full h-full object-cover rounded-xl" src="{{ asset('images/camilan.webp') }}" alt="">
                @endif
            </div>
            </a>
            <div class="flex-1">
                <a href="{{ route('kuliner.view', $umkm->id) }}" class="text-xl font-semibold text-[#111827] mb-2 capitalize ">{{ $umkm->nama_usaha }}</a>
                <p class="text-gray-500 mb-3">{{ $umkm->alamat }}</p>
                <div class="flex gap-3 flex-wrap">
                    <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#D92D20]">Rating : {{ $umkm->rating ?? "-" }}</span>

                    <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#D92D20]">Jam Operasional : {{ $umkm->jam_operasional ?? "-" }}</span>

                    @if ($umkm->kategori == 'makanan_khas')
                        <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">Makanan Khas</span>
                    @elseif ($umkm->kategori == 'makanan_berat')
                        <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">Makanan Berat</span>
                    @elseif ($umkm->kategori == 'minuman')
                        <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">Minuman</span>
                    @else
                        <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">Camilan/Oleh-oleh</span>
                    @endif

                    <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">{{ $umkm->subdistrict->name }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination (UI Ready) -->
    <div class="mt-12">
        {{-- <nav class="flex items-center gap-2">
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100">Prev</button>
            <button class="px-4 py-2 bg-[#D92D20] text-white rounded-lg text-sm">1</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100">2</button>
            <button class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100">Next</button>
        </nav> --}}
        {{ $umkms->links() }}
    </div>
</section>

@endsection
