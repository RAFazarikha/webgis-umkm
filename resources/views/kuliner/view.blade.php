@extends('layouts.app')
@section('title', $umkm->name . ' - Jelajah Rasa')
@section('content')

<section class="max-w-7xl mx-auto px-6 py-12">

    <!-- Breadcrumb -->
    {{-- <div class="mb-6 text-sm text-gray-500">
        <a href="/" class="hover:text-[#D92D20]">Beranda</a>
        <span class="mx-2">/</span>
        <a href="/kuliner" class="hover:text-[#D92D20]">Kuliner</a>
        <span class="mx-2">/</span>
        <span class="text-[#111827] font-medium">{{ $umkm->name }}</span>
    </div> --}}

    <div class="grid md:grid-cols-2 gap-12">
        <!-- Image Gallery -->
        {{-- <div>
            <div class="w-full h-96 rounded-2xl overflow-hidden shadow-md mb-4">
                <img src="{{ $umkm->image_url ?? '/images/placeholder.jpg' }}"
                    alt="{{ $umkm->nama_usaha }}"
                    class="w-full h-full object-cover hover:scale-105 transition duration-500" />
            </div>
        </div> --}}

        <!-- Detail Info -->
        <div>
            <h1 class="text-4xl font-bold text-[#111827] mb-4 capitalize">
                {{ $umkm->nama_usaha }}
            </h1>

            <div class="flex gap-3 flex-wrap mb-6">

                @if ($umkm->kategori == 'makanan_khas')
                    <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">
                        Makanan Khas
                    </span>
                @elseif ($umkm->kategori == 'makanan_berat')
                    <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">
                        Makanan Berat
                    </span>
                @elseif ($umkm->kategori == 'minuman')
                    <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">
                        Minuman
                    </span>
                @else
                    <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">
                        Camilan/Oleh-oleh
                    </span>
                @endif

                <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">
                    {{ $umkm->subdistrict->name }}
                </span>
            </div>

            <p class="text-gray-600 leading-relaxed mb-6">
                {{ $umkm->alamat }}
            </p>

            <div class="space-y-4 text-sm">
                <div class="flex items-center gap-3">
                    <span class="font-semibold text-[#111827]">Jam Operasional:</span>
                    <span class="text-gray-600">{{ $umkm->jam_operasional }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex gap-4">
                <a href="{{ route('map') }}?lat={{ $umkm->latitude }}&lng={{ $umkm->longitude }}"
                   class="px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
                    Lihat di Peta
                </a>

                <a href="/kuliner"
                   class="px-6 py-3 border border-[#111827] text-[#111827] rounded-lg hover:bg-[#111827] hover:text-white transition">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Map Preview -->
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-[#111827] mb-6">Lokasi</h2>
        <div id="detailMap" class="w-full h-[400px] rounded-2xl border border-gray-200 shadow-sm"></div>
    </div>

</section>

<!-- Leaflet CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $umkm->latitude ?? -7.0049 }};
        const lng = {{ $umkm->longitude ?? 113.8595 }};

        const map = L.map('detailMap').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        L.marker([lat, lng], { icon: redIcon })
            .addTo(map)
            .bindPopup(`<strong class="capitalize">{{ $umkm->nama_usaha }}</strong>`)
            .openPopup();
    });
</script>

@endsection
