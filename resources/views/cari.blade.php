@extends('layouts.app')
@section('title', 'Cari - Peta Kuliner Sumenep')

{{-- Menyiapkan variabel bantu agar penulisan lebih rapi --}}
@php
    $description = "Peta Kuliner Sumenep adalah platform yang memetakan berbagai destinasi kuliner di Kabupaten Sumenep, mulai dari makanan khas, makanan berat, minuman, hingga camilan/oleh-oleh. Jelajahi ragam kuliner terbaik di Sumenep dengan sistem pemetaan spasial kami yang mudah digunakan.";

    // Mengecek apakah UMKM punya foto, jika tidak biarkan kosong agar memakai default dari layout
    $imageUrl = asset('images/hero-kuliner.webp');
@endphp

{{-- Mengisi Yield di Layout --}}
@section('meta_description', $description)

@section('meta_image', $imageUrl)

@section('meta_type', 'website')

@section('content')

<section class="max-w-7xl mx-auto px-6 py-12">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-[#111827] mb-4">Peta Kuliner Sumenep</h1>
        <p class="text-gray-500">Explore the island with ease.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm mb-6">
        <form action="/cari" method="GET" class="grid md:grid-cols-3 gap-6 items-end">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-2">Search</label>
                <div class="relative">
                    <input type="text" name="search" placeholder="Search for culinary spots..."
                        value="{{ request('search') }}"
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
        </form>
    </div>

    <!-- Map -->
    <div id="map" class="w-full h-[500px] rounded-2xl shadow-md border border-gray-200 z-10"></div>

    <!-- Culinary List -->
    <div class="space-y-6 mt-6">
        @if ($umkms->isEmpty())
            <p class="text-gray-500 text-center">- Data UMKM Kuliner tidak ditemukan -</p>
        @endif
        @foreach ($umkms as $umkm)
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition flex flex-col md:flex-row gap-6 items-center justify-center text-center md:text-left md:items-start md:justify-start">
            <a href="{{ route('kuliner.view', $umkm->slug) }}">
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
                <a href="{{ route('kuliner.view', $umkm->slug) }}" class="text-xl font-semibold text-[#111827] mb-2 capitalize ">{{ $umkm->nama_usaha }}</a>
                <p class="text-gray-500 mb-3">{{ $umkm->alamat }}</p>
                <div class="flex gap-3 flex-wrap">
                    <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">Rating : {{ $umkm->rating ?? "-" }}</span>

                    <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">Jam Operasional : {{ $umkm->jam_operasional ?? "-" }}</span>

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
{{--
cluster: @json($clusterExists
                    ? (optional($umkm->clusterResultAll->first())->cluster ?? 'noise')
                    : 'data belum di cluster'),
--}}
<style>
    .legend {
        background: white;
        padding: 10px 12px;
        font-size: 12px;
        border-radius: 6px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        line-height: 18px;
    }

    .legend i {
        float: left;
        margin-right: 8px;
        opacity: 0.8;
    }
</style>

<!-- Leaflet CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const map = L.map('map').setView([-7.0049, 113.8595], 12);

        map.createPane('markerPaneCustom');
        map.getPane('markerPaneCustom').style.zIndex = 650;

        map.createPane('tooltipPaneCustom');
        map.getPane('tooltipPaneCustom').style.zIndex = 700;

        let activeMarker = null;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const locations = [
            @foreach($umkms as $umkm)
            {
                lat: {{ $umkm->latitude }},
                lng: {{ $umkm->longitude }},
                name: @json($umkm->nama_usaha),
                category: @json($umkm->kategori),
                district: @json($umkm->subdistrict->name),
                address: @json($umkm->alamat),
                open_hours: @json($umkm->jam_operasional ?? '-'),
                cluster: @json(optional($umkm->clusterResultAll->first())->cluster ?? 'noise'),
                slug: @json($umkm->slug),
                detail_url: @json(route('kuliner.view', $umkm->slug))
            },
            @endforeach
        ];

        // Fungsi untuk menentukan radius dinamis berdasarkan level zoom
        function getDynamicRadius(isActive = false) {
            const zoom = map.getZoom();
            // Contoh perhitungan: di zoom 12, radius normal = 4. Batas minimum radius = 2.
            const normalRadius = Math.max(2, zoom - 4);

            // Jika marker sedang aktif, ukurannya 2x lipat lebih besar
            return isActive ? normalRadius * 2 : normalRadius;
        }

        function highlightMarker(marker) {
            // reset marker sebelumnya
            if (activeMarker) {
                activeMarker.setStyle({
                    radius: getDynamicRadius(false), // Gunakan fungsi dinamis
                    color: "#ffffff",
                    weight: 1,
                    fillOpacity: 0.9
                });
            }

            // set marker baru jadi aktif
            marker.setStyle({
                radius: getDynamicRadius(true),  // Gunakan fungsi dinamis (aktif)
                color: "#000000",
                weight: 2,
                fillOpacity: 1
            });

            activeMarker = marker;
        }

        // ===============================
        // HITUNG JUMLAH UMKM PER KECAMATAN
        // ===============================

        const umkmCount = {};

        locations.forEach(loc => {
            // NORMALISASI: Ubah semua nama kecamatan ke huruf besar agar cocok dengan GeoJSON
            const districtName = loc.district ? loc.district.toUpperCase().trim() : 'UNKNOWN';

            if (!umkmCount[districtName]) {
                umkmCount[districtName] = 0;
            }
            umkmCount[districtName]++;
        });

        const counts = Object.values(umkmCount);
        const maxCount = counts.length > 0 ? Math.max(...counts) : 0;

        const classCount = 6; // jumlah kelas warna
        const interval = Math.ceil(maxCount / classCount) || 1; // || 1 mencegah pembagian 0 atau error interval

        const grades = [];
        for (let i = 0; i <= maxCount; i += interval) {
            grades.push(i);
        }

        // ===============================
        // FUNGSI WARNA CHOROPLETH
        // ===============================

        function getDistrictColor(count) {
            for (let i = grades.length - 1; i >= 0; i--) {
                if (count >= grades[i]) {
                    const colors = [
                        '#FFEDA0',
                        '#FED976',
                        '#FEB24C',
                        '#FD8D3C',
                        '#FC4E2A',
                        '#E31A1C',
                        '#BD0026'
                    ];
                    return colors[i] || colors[colors.length - 1];
                }
            }
            return '#FFEDA0';
        }

        function style(feature) {
            // NORMALISASI: Ambil WADMKC dan pastikan formatnya uppercase
            const kecamatan = feature.properties.WADMKC ? feature.properties.WADMKC.toUpperCase().trim() : '';
            const count = umkmCount[kecamatan] || 0;

            return {
                fillColor: getDistrictColor(count),
                weight: 1,
                opacity: 1,
                color: '#999', // Sedikit lebih terang dari #555 agar batas desa tidak menutupi warna fill
                fillOpacity: 0.7
            };
        }

        // ===============================
        // LOAD GEOJSON KECAMATAN/DESA
        // ===============================

        const districtLayer = L.geoJSON(null, {
            style: style,
            onEachFeature: function(feature, layer) {
                // Ambil nama kecamatan dan nama desa, berikan fallback jika null
                const kecamatan = feature.properties.WADMKC ? feature.properties.WADMKC.toUpperCase().trim() : 'TIDAK DIKETAHUI';
                const desa = feature.properties.NAMOBJ ? feature.properties.NAMOBJ : '-';

                const count = umkmCount[kecamatan] || 0;

                // Modifikasi tooltip dengan styling yang rapi
                layer.bindTooltip(
                    `<div style="text-align:center;">
                        <b>Kecamatan ${kecamatan}</b><br>
                        <span style="font-size: 11px; color: #555;">Desa: ${desa}</span><hr style="margin: 4px 0;">
                        Jumlah UMKM: <b>${count}</b>
                    </div>`,
                    {
                        pane: 'tooltipPaneCustom',
                        sticky: true,
                        className: 'custom-district-tooltip' // Bisa dikustomisasi di CSS
                    }
                );
            }
        });

        fetch('/geojson/adm_desa.json') // Sesuaikan path ini dengan folder publik Anda
            .then(res => res.json())
            .then(data => {
                districtLayer.addData(data);
            })
            .catch(error => console.error("Error loading GeoJSON:", error));

        // ===============================
        // LAYER TITIK UMKM
        // ===============================

        const umkmLayer = L.layerGroup();
        const markerIndex = {};

        locations.forEach(loc => {
            // Asumsi fungsi getClusterColor sudah ada di file js Anda
            // Jika belum, pastikan Anda menambahkannya.
            const markerColor = typeof getClusterColor === 'function' ? getClusterColor(loc.cluster) : '#3388ff';

            const marker = L.circleMarker([loc.lat, loc.lng], {
                pane: 'markerPaneCustom',
                radius: getDynamicRadius(false),
                fillColor: markerColor,
                color: "#ffffff",
                weight: 1,
                fillOpacity: 0.9
            })
            .bindPopup(`<b class="capitalize">${loc.name}</b><br>Cluster: ${loc.cluster}`)
            .on('click', function () {
                highlightMarker(this);
            })
            .openPopup();

            markerIndex[loc.name] = marker;
            umkmLayer.addLayer(marker);
        });

        // ===============================
        // TAMBAHKAN KE MAP
        // ===============================

        districtLayer.addTo(map); // Tambahkan district dulu agar berada di bawah titik UMKM
        umkmLayer.addTo(map);

        // ===============================
        // LAYER CONTROL
        // ===============================

        const overlayMaps = {
            "Batas Wilayah (Desa/Kec)": districtLayer,
            "Titik UMKM": umkmLayer
        };

        L.control.layers(null, overlayMaps).addTo(map);

        // ===============================
        // LEGENDA CHOROPLETH
        // ===============================

        const legend = L.control({ position: "bottomright" });

        legend.onAdd = function () {
            const div = L.DomUtil.create("div", "info legend");

            // CSS in-line sederhana untuk box legenda, pindahkan ke style.css jika perlu
            div.style.backgroundColor = "white";
            div.style.padding = "8px";
            div.style.borderRadius = "5px";
            div.style.boxShadow = "0 0 15px rgba(0,0,0,0.2)";

            div.innerHTML += "<b style='margin-bottom:5px; display:block;'>Jumlah UMKM</b>";

            for (let i = 0; i < grades.length; i++) {
                const from = grades[i];
                const to = grades[i + 1];

                div.innerHTML +=
                    '<i style="background:' + getDistrictColor(from + 1) +
                    '; width:18px; height:18px; display:inline-block; margin-right:8px; vertical-align: middle;"></i> ' +
                    from + (to ? '&ndash;' + to + '<br>' : '+');
            }

            return div;
        };

        legend.addTo(map);

        // ===============================
        // RESIZE MARKER OTOMATIS SAAT ZOOM
        // ===============================
        map.on('zoomend', function() {
            umkmLayer.eachLayer(function(marker) {
                // Cek apakah marker ini sedang aktif atau tidak
                const isActive = (marker === activeMarker);

                // Update ukurannya
                marker.setRadius(getDynamicRadius(isActive));
            });
        });

    });


    function getClusterColor(cluster) {

        const colors = [
            "#e6194B", // merah
            "#3cb44b", // hijau
            "#4363d8", // biru
            "#f58231", // orange
            "#911eb4", // ungu
            "#46f0f0", // cyan
            "#f032e6", // magenta
            "#bcf60c", // lime
            "#fabebe", // pink muda
            "#008080", // teal
            "#e6beff", // lavender
            "#9a6324", // coklat
            "#fffac8", // kuning pucat
            "#800000", // maroon
            "#aaffc3", // mint
            "#808000", // olive
            "#ffd8b1", // peach
            "#000075", // navy
            "#808080", // grey
            "#ff0000", // red bright
            "#00ff00", // green bright
            "#0000ff", // blue bright
            "#ff00ff", // magenta bright
            "#00ffff", // cyan bright
            "#ff9900", // orange strong
            "#66ff33", // lime strong
            "#cc00ff", // purple strong
            "#ff3366", // pink strong
            "#0099cc", // sky blue
            "#33cc99"  // aqua green
        ];

        return colors[cluster] ?? "#6b7280"; // abu untuk noise
    }


</script>

@endsection
