@extends('layouts.app')
@section('title', 'Map - Jelajah Rasa')
@section('content')

<section class="max-w-7xl mx-auto px-6 py-12">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-[#111827] mb-4">Jelajah Rasa</h1>
        <p class="text-gray-500">Explore the island with ease.</p>
    </div>

    <!-- Filters -->
    <div x-data="filterHandler()" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mb-10">
        <h2 class="text-2xl font-semibold text-[#111827] mb-6">Filters</h2>

        <div class="grid md:grid-cols-2 gap-8">
            <div>
                <label class="block text-sm font-medium mb-2">Kecamatan</label>

                <select x-model="kecamatan"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">

                    <option value="all">Semua Kecamatan</option>

                    @foreach($kecamatans as $kecamatanItem)
                        <option value="{{ $kecamatanItem->name }}">
                            {{ $kecamatanItem->name }}
                        </option>
                    @endforeach

                </select>

                <p class="text-xs text-gray-400 mt-1">Pilih kecamatan.</p>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Filter by</label>
                <div class="flex flex-wrap gap-3">
                    <template x-for="category in categories" :key="category">
                        <button @click="toggle(category)"
                            :class="selected.includes(category)
                                ? 'bg-[#D92D20] text-white border-[#D92D20]'
                                : 'bg-white text-gray-700 border-gray-300'"
                            class="px-4 py-2 rounded-full border text-sm transition">
                            <span x-text="category"></span>
                        </button>
                    </template>
                </div>
                <p class="text-xs text-gray-400 mt-1">Select categories.</p>
            </div>
        </div>

        <div class="mt-6">
            <button @click="applyFilter()"
                class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-[#D92D20] transition">
                Apply Filters
            </button>
        </div>
    </div>

    <!-- Map -->
    <div id="map" class="w-full h-[500px] rounded-2xl shadow-md border border-gray-200 z-10"></div>

    <!-- Sidebar Wrapper -->
    <div x-data="sidebarHandler()" x-cloak class="z-[999]">

        <!-- Backdrop -->
        <div x-show="open"
            x-transition.opacity
            @click="close()"
            class="fixed inset-0 bg-[#111827]/60 z-[998]">
        </div>

        <!-- Sidebar Panel -->
        <div x-show="open"
            @click.outside="close()"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 w-full md:w-[420px] h-full bg-white shadow-2xl z-[999] overflow-y-auto">

            <!-- Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-[#111827] capitalize" x-text="data.name"></h2>
                <button @click="close()" class="text-gray-400 hover:text-[#D92D20] text-2xl">
                    &times;
                </button>
            </div>

            <!-- Content -->
            <div class="p-6 space-y-6">

                <div class="w-full h-52 bg-gray-200 rounded-xl overflow-hidden">
                    <img :src="data.image ?? '/images/placeholder.jpg'"
                        class="w-full h-full object-cover"
                        alt="">
                </div>

                <div class="flex gap-2 flex-wrap">
                    <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]"
                        x-text="data.district"></span>
                    <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]"
                        x-text="data.category"></span>
                </div>

                <p class="text-sm text-gray-600 leading-relaxed"
                x-text="data.description"></p>

                <div class="text-sm space-y-2">
                    <div>
                        <span class="font-semibold text-[#111827]">Alamat:</span>
                        <span class="text-gray-600" x-text="data.address"></span>
                    </div>
                    <div>
                        <span class="font-semibold text-[#111827]">Jam:</span>
                        <span class="text-gray-600" x-text="data.open_hours"></span>
                    </div>
                </div>

                <a :href="data.detail_url"
                class="block text-center px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
                Lihat Detail
                </a>

            </div>
        </div>

    </div>
</section>

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
<script src="https://unpkg.com/alpinejs" defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const map = L.map('map').setView([-7.0049, 113.8595], 12);

        map.createPane('markerPaneCustom');
        map.getPane('markerPaneCustom').style.zIndex = 650;

        map.createPane('tooltipPaneCustom');
        map.getPane('tooltipPaneCustom').style.zIndex = 700;

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
                cluster: @json(optional($umkm->clusterResultAll->first())->cluster ?? 'Noise'),
                detail_url: @json(route('kuliner.view', $umkm->id))
            },
            @endforeach
            ];

        // ===============================
        // HITUNG JUMLAH UMKM PER KECAMATAN
        // ===============================

        const umkmCount = {};

        locations.forEach(loc => {
            if (!umkmCount[loc.district]) {
                umkmCount[loc.district] = 0;
            }
            umkmCount[loc.district]++;
        });

        const counts = Object.values(umkmCount);
        const maxCount = Math.max(...counts);

        const classCount = 6; // jumlah kelas warna
        const interval = Math.ceil(maxCount / classCount);

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

            // nama kecamatan dari geojson
            const kecamatan = feature.properties.nm_kecamatan || feature.properties.name;

            const count = umkmCount[kecamatan] || 0;

            return {
                fillColor: getDistrictColor(count),
                weight: 1,
                opacity: 1,
                color: '#555',
                fillOpacity: 0.7
            };
        }

        // ===============================
        // LOAD GEOJSON KECAMATAN
        // ===============================

        const districtLayer = L.geoJSON(null, {

            style: style,

            onEachFeature: function(feature, layer) {

                const kecamatan = feature.properties.nm_kecamatan || feature.properties.name;
                const count = umkmCount[kecamatan] || 0;

                layer.bindTooltip(
                    `<b>Kecamatan ${kecamatan}</b><br>Jumlah UMKM: ${count}`,
                    {
                        pane: 'tooltipPaneCustom',
                        sticky: true
                    }
                );
            }

        });

        fetch('/geojson/35.29_kecamatan.geojson') // letakkan file di public/geojson
            .then(res => res.json())
            .then(data => {
                districtLayer.addData(data);
            });

        // ===============================
        // LAYER TITIK UMKM
        // ===============================

        const umkmLayer = L.layerGroup();

        locations.forEach(loc => {

            const marker = L.circleMarker([loc.lat, loc.lng], {
                pane: 'markerPaneCustom',
                radius: 4,
                fillColor: getClusterColor(loc.cluster),
                color: "#ffffff",
                weight: 1,
                fillOpacity: 0.9
            })
            .bindTooltip(
                `<b class="capitalize">${loc.name}</b><br>Cluster: ${loc.cluster}`
            , {
                pane: 'tooltipPaneCustom'
            })
            .on('click', function () {
                window.dispatchEvent(new CustomEvent('open-sidebar', {
                    detail: loc
                }));
            });

            umkmLayer.addLayer(marker);

        });

        // ===============================
        // TAMBAHKAN KE MAP
        // ===============================

        umkmLayer.addTo(map);
        districtLayer.addTo(map);

        // ===============================
        // LAYER CONTROL
        // ===============================

        const overlayMaps = {
            "Batas Kecamatan": districtLayer,
            "Titik UMKM": umkmLayer
        };

        L.control.layers(null, overlayMaps).addTo(map);

        // ===============================
        // LEGENDA CHOROPLETH
        // ===============================

        const legend = L.control({ position: "bottomright" });

        legend.onAdd = function () {

            const div = L.DomUtil.create("div", "info legend");

            div.innerHTML += "<b>Jumlah UMKM</b><br>";

            for (let i = 0; i < grades.length; i++) {

                const from = grades[i];
                const to = grades[i + 1];

                div.innerHTML +=
                    '<i style="background:' + getDistrictColor(from + 1) +
                    '; width:18px; height:18px; display:inline-block; margin-right:8px;"></i> ' +
                    from + (to ? '&ndash;' + to + '<br>' : '+');

            }

            return div;
        };

        legend.addTo(map);

    });

    function filterHandler() {
        return {
            kecamatan: 'all',
            categories: ['Makanan Berat', 'Camilan/Oleh-Oleh', 'Makanan Khas'],
            selected: [],

            toggle(category) {
                if (this.selected.includes(category)) {
                    this.selected = this.selected.filter(c => c !== category);
                } else {
                    this.selected.push(category);
                }
            },

            applyFilter() {

                let kategori = this.selected.length ? this.selected[0] : 'all'

                const params = new URLSearchParams({
                    kecamatan: this.kecamatan,
                    kategori: kategori
                });

                window.location.href = `/map?${params.toString()}`
            }
        }
    }

    function sidebarHandler() {
        return {
            open: false,
            data: {},

            init() {
                window.addEventListener('open-sidebar', (event) => {
                    this.data = event.detail;
                    this.open = true;
                });
            },

            close() {
                this.open = false;
            }
        }
    }

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
