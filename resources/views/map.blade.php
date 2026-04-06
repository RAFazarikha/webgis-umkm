@extends('layouts.app')
@section('title', 'Map - Peta Kuliner Sumenep')
@section('content')

<section class="max-w-7xl mx-auto px-6 py-12">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-[#111827] mb-4">Peta Kuliner Sumenep</h1>
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
                            :class="selected === category
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
                class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-white border hover:border-[#111827] hover:text-[#111827] transition">
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

                    <template x-if="data.category === 'makanan_khas'">
                        <img :src="'{{ asset('images/makanan-khas.webp') }}'" class="w-full h-full object-cover" alt="">
                    </template>

                    <template x-if="data.category === 'makanan_berat'">
                        <img :src="'{{ asset('images/makanan-berat.webp') }}'" class="w-full h-full object-cover" alt="">
                    </template>

                    <template x-if="data.category === 'minuman'">
                        <img :src="'{{ asset('images/minuman.webp') }}'" class="w-full h-full object-cover" alt="">
                    </template>

                    <template x-if="data.category === 'camilan_oleh_oleh'">
                        <img :src="'{{ asset('images/camilan.webp') }}'" class="w-full h-full object-cover" alt="">
                    </template>

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
                    <div>
                        <span class="font-semibold text-[#111827]">Cluster:</span>
                        <span class="text-gray-600" x-text="data.cluster"></span>
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
                detail_url: @json(route('kuliner.view', $umkm->id))
            },
            @endforeach
        ];

        function highlightMarker(marker) {
            // reset marker sebelumnya
            if (activeMarker) {
                activeMarker.setStyle({
                    radius: 4,
                    color: "#ffffff",
                    weight: 1,
                    fillOpacity: 0.9
                });
            }

            // set marker baru jadi aktif
            marker.setStyle({
                radius: 8,              // lebih besar
                color: "#000000",       // outline beda
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

        const selectedUmkm = @json($selectedUmkm ?? null);

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
                radius: 4,
                fillColor: markerColor,
                color: "#ffffff",
                weight: 1,
                fillOpacity: 0.9
            })
            .bindTooltip(`<b class="capitalize">${loc.name}</b><br>Cluster: ${loc.cluster}`, {
                pane: 'tooltipPaneCustom'
            })
            .on('click', function () {
                highlightMarker(this);
                window.dispatchEvent(new CustomEvent('open-sidebar', {
                    detail: loc
                }));
            });

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

        if (selectedUmkm) {
            const marker = markerIndex[selectedUmkm.nama_usaha];

            if (marker) {
                const latlng = marker.getLatLng();
                map.setView(latlng, 16);
                highlightMarker(marker);

                window.dispatchEvent(new CustomEvent('open-sidebar', {
                    detail: {
                        lat: selectedUmkm.latitude,
                        lng: selectedUmkm.longitude,
                        name: selectedUmkm.nama_usaha,
                        category: selectedUmkm.kategori,
                        district: selectedUmkm.subdistrict.name,
                        address: selectedUmkm.alamat,
                        open_hours: selectedUmkm.jam_operasional ?? '-',
                        detail_url: `/kuliner/${selectedUmkm.id}` // Sesuaikan route sesuai struktur Anda
                    }
                }));
            }
        }

    });



    function filterHandler() {

        const urlParams = new URLSearchParams(window.location.search);

        const kecamatanParam = urlParams.get('kecamatan') || 'all';
        const kategoriParam = urlParams.get('kategori') || 'all';

        const reverseCategoryMap = {
            'makanan_berat': 'Makanan Berat',
            'makanan_khas': 'Makanan Khas',
            'camilan_oleh_oleh': 'Camilan/Oleh-Oleh'
        };

        return {
            kecamatan: kecamatanParam,
            categories: ['Makanan Berat', 'Camilan/Oleh-Oleh', 'Makanan Khas'],
            selected: kategoriParam !== 'all'
                ? reverseCategoryMap[kategoriParam]
                : 'all',

            toggle(category) {
                if (this.selected === category) {
                    this.selected = 'all';
                } else {
                    this.selected = category;
                }
            },

            applyFilter() {

                const categoryMap = {
                    'Makanan Berat': 'makanan_berat',
                    'Makanan Khas': 'makanan_khas',
                    'Camilan/Oleh-Oleh': 'camilan_oleh_oleh'
                };

                let kategori = this.selected !== 'all'
                    ? categoryMap[this.selected]
                    : 'all';

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
