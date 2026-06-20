@extends('layouts.app')
@section('title', 'Map - Peta Kuliner Sumenep')

{{-- Menyiapkan variabel bantu agar penulisan lebih rapi --}}
@php
    $description =
        'Peta Kuliner Sumenep adalah platform yang memetakan berbagai destinasi kuliner di Kabupaten Sumenep, mulai dari makanan khas, makanan berat, minuman, hingga camilan/oleh-oleh. Jelajahi ragam kuliner terbaik di Sumenep dengan sistem pemetaan spasial kami yang mudah digunakan.';

    // Mengecek apakah UMKM punya foto, jika tidak biarkan kosong agar memakai default dari layout
    $imageUrl = asset('images/hero-kuliner.webp');
@endphp

{{-- Mengisi Yield di Layout --}}
@section('meta_description', $description)

@section('meta_image', $imageUrl)

@section('meta_type', 'website')

@section('content')

    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="text-center mb-10 justify-center items-center">
            <h1 class="text-4xl font-bold text-[#111827] mb-4">Peta Kuliner Sumenep</h1>

            <p class="text-gray-500 max-w-3xl text-center mx-auto leading-relaxed">
                Penerapan algoritma DBSCAN pada <b>{{ $umkms->count() }}</b> data UMKM kuliner berhasil memetakan sebaran
                lokasi usaha.

                @if ($wilayah === 'all')
                    Di <b>Daratan Utama</b> terbentuk <b>{{ $jmlClusterDaratan }} cluster</b> (Eps: {{ $epsDaratan }},
                    MinPts: {{ $minptsDaratan }}),
                    sedangkan di <b>Kepulauan</b> terbentuk <b>{{ $jmlClusterKepulauan }} cluster</b> (Eps:
                    {{ $epsKepulauan }}, MinPts: {{ $minptsKepulauan }}).
                @elseif($wilayah === 'daratan_utama')
                    Di wilayah <b>Daratan Utama</b>, algoritma berhasil membentuk <b>{{ $jmlClusterDaratan }} cluster</b>
                    dengan parameter Epsilon: {{ $epsDaratan }} dan MinPts: {{ $minptsDaratan }}.
                @elseif($wilayah === 'kepulauan')
                    Di wilayah <b>Kepulauan</b>, algoritma berhasil membentuk <b>{{ $jmlClusterKepulauan }} cluster</b>
                    dengan parameter Epsilon: {{ $epsKepulauan }} dan MinPts: {{ $minptsKepulauan }}.
                @endif
            </p>
        </div>

        <!-- Filters -->
        <div x-data="filterHandler()" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mb-10">
            <h2 class="text-2xl font-semibold text-[#111827] mb-6">Filters</h2>

            <div class="grid md:grid-cols-2 gap-8">

                <div>
                    <label class="block text-sm font-medium mb-2">Kecamatan</label>

                    <select x-model="kecamatan" class="w-full px-4 py-2 border border-gray-300 rounded-lg">

                        <option value="all">Semua Kecamatan</option>

                        @foreach ($kecamatans as $kecamatanItem)
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
                                :class="selected === category ?
                                    'bg-[#D92D20] text-white border-[#D92D20]' :
                                    'bg-white text-gray-700 border-gray-300'"
                                class="px-4 py-2 rounded-full border text-sm transition">
                                <span x-text="category"></span>
                            </button>
                        </template>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Select categories.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Kategori Wilayah</label>

                    <select x-model="kategori_wilayah" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="all">Semua Wilayah</option>
                        <option value="daratan_utama">Daratan Utama</option>
                        <option value="kepulauan">Kepulauan</option>
                    </select>

                    <p class="text-xs text-gray-400 mt-1">Pilih kategori wilayah (daratan utama atau kepulauan).</p>
                </div>

                <div class="flex items-center">
                    <button @click="applyFilter()"
                        class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-white border hover:border-[#111827] hover:text-[#111827] transition">
                        Apply Filters
                    </button>
                </div>
            </div>

        </div>

        <!-- Map -->
        <div id="map" class="w-full h-[700px] rounded-2xl shadow-md border border-gray-200 z-10"></div>

        <!-- Filters Cluster -->
        <div x-data="filterHandler()" class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm mt-10">
            <h2 class="text-2xl font-semibold text-[#111827] mb-6">Filter Cluster</h2>

            <div class="grid md:grid-cols-1 gap-8">
                <div>
                    <form method="GET" action="/cluster">

                        <label class="block text-sm font-medium mb-2">Cluster</label>

                        <select x-model="cluster" name="cluster" class="w-full px-4 py-2 border border-gray-300 rounded-lg">

                            <option value="all">Semua Cluster</option>

                            @foreach ($clusters as $clusterItem)
                                <option value="{{ $clusterItem }}">
                                    Cluster {{ $clusterItem }}
                                </option>
                            @endforeach

                            <option value="noise">Noise</option>

                        </select>

                        <p class="text-xs text-gray-400 mt-1">Pilih cluster.</p>
                </div>

            </div>

            <div class="mt-6">

                <button class="me-2 px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
                    Lihat Detail Cluster
                </button>

                </form>
                <button @click="applyFilter()"
                    class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-white border hover:border-[#111827] hover:text-[#111827] transition">
                    Apply Filters
                </button>
            </div>
        </div>

        <!-- Sidebar Wrapper -->
        <div x-data="sidebarHandler()" x-cloak class="z-[999]">

            <!-- Backdrop -->
            <div x-show="open" x-transition.opacity @click="close()" class="fixed inset-0 bg-[#111827]/60 z-[998]">
            </div>

            <!-- Sidebar Panel -->
            <div x-show="open" @click.outside="close()" x-transition:enter="transform transition ease-in-out duration-300"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
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
                            <img :src="'{{ asset('images/makanan-khas.webp') }}'" class="w-full h-full object-cover"
                                alt="">
                        </template>

                        <template x-if="data.category === 'makanan_berat'">
                            <img :src="'{{ asset('images/makanan-berat.webp') }}'" class="w-full h-full object-cover"
                                alt="">
                        </template>

                        <template x-if="data.category === 'minuman'">
                            <img :src="'{{ asset('images/minuman.webp') }}'" class="w-full h-full object-cover"
                                alt="">
                        </template>

                        <template x-if="data.category === 'camilan_oleh_oleh'">
                            <img :src="'{{ asset('images/camilan.webp') }}'" class="w-full h-full object-cover"
                                alt="">
                        </template>

                    </div>

                    <div class="flex gap-2 flex-wrap">
                        <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]"
                            x-text="data.district"></span>
                        <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]"
                            x-text="data.category"></span>
                    </div>

                    <p class="text-sm text-gray-600 leading-relaxed" x-text="data.description"></p>

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

    <style>
        .legend {
            background: white;
            padding: 10px 12px;
            font-size: 12px;
            border-radius: 6px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
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
        document.addEventListener('DOMContentLoaded', function() {

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
                @foreach ($umkms as $umkm)
                    {
                        lat: {{ $umkm->latitude }},
                        lng: {{ $umkm->longitude }},
                        name: @json($umkm->nama_usaha),
                        category: @json($umkm->kategori),
                        district: @json($umkm->subdistrict->name),
                        kategori_wilayah: @json($umkm->subdistrict->kategori_wilayah),
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
                const normalRadius = Math.max(2, zoom - 8);

                // Jika marker sedang aktif, ukurannya 2x lipat lebih besar
                return isActive ? normalRadius * 2 : normalRadius;
            }

            function highlightMarker(marker) {
                // reset marker sebelumnya
                if (activeMarker) {
                    activeMarker.setStyle({
                        radius: getDynamicRadius(false),
                        color: "#ffffff",
                        weight: 1,
                        fillOpacity: 1
                    });
                }

                // set marker baru jadi aktif
                marker.setStyle({
                    radius: getDynamicRadius(true), // lebih besar
                    color: "#000000", // outline beda
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
                    const kecamatan = feature.properties.WADMKC ? feature.properties.WADMKC
                        .toUpperCase().trim() : 'TIDAK DIKETAHUI';
                    const desa = feature.properties.NAMOBJ ? feature.properties.NAMOBJ : '-';

                    const count = umkmCount[kecamatan] || 0;

                    // Modifikasi tooltip dengan styling yang rapi
                    layer.bindTooltip(
                        `<div style="text-align:center;">
                        <b>Kecamatan ${kecamatan}</b><br>
                        <span style="font-size: 11px; color: #555;">Desa: ${desa}</span><hr style="margin: 4px 0;">
                        Jumlah UMKM: <b>${count}</b>
                    </div>`, {
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
                const style = getClusterStyle(loc.cluster, loc.kategori_wilayah);

                const marker = L.circleMarker([loc.lat, loc.lng], {
                        pane: 'markerPaneCustom',
                        radius: getDynamicRadius(false),
                        fillColor: style.fillColor,
                        color: style.color, // Warna outline
                        weight: style.weight, // Ketebalan outline
                        fillOpacity: 1
                    })
                    // Tambahkan keterangan wilayah di tooltip agar lebih jelas
                    .bindTooltip(
                        `<b class="capitalize">${loc.name}</b><br>Wilayah: ${loc.kategori_wilayah}<br>Cluster: ${loc.cluster}`, {
                            pane: 'tooltipPaneCustom'
                        })
                    .on('click', function() {
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

            const legend = L.control({
                position: "bottomright"
            });

            legend.onAdd = function() {
                const div = L.DomUtil.create("div", "info legend");

                // CSS in-line sederhana untuk box legenda, pindahkan ke style.css jika perlu
                div.style.backgroundColor = "white";
                div.style.padding = "8px";
                div.style.borderRadius = "5px";
                div.style.boxShadow = "0 0 15px rgba(0,0,0,0.2)";
                // div.style.marginBottom = "70px";

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

            // ===============================
            // LEGENDA CLUSTER UMKM (Kiri Bawah, Memanjang)
            // ===============================

            const clusterLegend = L.control({
                position: "bottomleft"
            });

            clusterLegend.onAdd = function() {
                const div = L.DomUtil.create("div", "info legend cluster-legend");

                div.style.backgroundColor = "white";
                div.style.padding = "8px 15px";
                div.style.borderRadius = "5px";
                div.style.boxShadow = "0 0 15px rgba(0,0,0,0.2)";
                div.style.marginRight = "140px";
                div.style.maxHeight = "200px"; // Beri batas tinggi agar tidak menutupi layar
                div.style.overflowY = "auto"; // Beri scroll jika clusternya puluhan

                let htmlContent = "<b style='margin-bottom: 5px; display:block;'>Legenda Cluster</b>";

                // 1. Ekstrak data unik berpasangan {cluster, wilayah}
                const uniqueClusters = [];
                const seen = new Set();

                locations.forEach(loc => {
                    // Buat identitas unik
                    const id = `${loc.kategori_wilayah}-${loc.cluster}`;
                    if (!seen.has(id)) {
                        seen.add(id);
                        uniqueClusters.push({
                            cluster: loc.cluster,
                            wilayah: loc.kategori_wilayah
                        });
                    }
                });

                // 2. Pisahkan data Noise agar selalu ditaruh di paling bawah (opsional tapi rapi)
                const realClusters = uniqueClusters.filter(c => c.cluster !== 'noise');
                const noiseClusters = uniqueClusters.filter(c => c.cluster === 'noise');

                // 3. Urutkan berdasarkan Wilayah, lalu Nomor Cluster
                realClusters.sort((a, b) => {
                    if (a.wilayah !== b.wilayah) {
                        return a.wilayah.localeCompare(b.wilayah);
                    }
                    return a.cluster - b.cluster;
                });

                const sortedClusters = [...realClusters, ...noiseClusters];

                // 4. Render HTML. Gunakan display Grid agar dua kolom jika terlalu panjang
                htmlContent +=
                    `<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 8px;">`;

                sortedClusters.forEach(item => {
                    const style = getClusterStyle(item.cluster, item.wilayah);

                    // Format teks wilayah agar mudah dibaca (daratan_utama -> Daratan)
                    const displayWilayah = item.wilayah === 'daratan_utama' ? 'Daratan' : 'Kepulauan';
                    const displayCluster = item.cluster === 'noise' ? 'Noise' :
                        `Cluster ${item.cluster}`;

                    htmlContent += `
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <i style="background: ${style.fillColor}; width: 14px; height: 14px; border-radius: 50%; display: inline-block; border: ${style.weight}px solid ${style.color}; flex-shrink: 0;"></i>
                        <span style="font-size: 11px; white-space: nowrap;">${displayWilayah}: ${displayCluster}</span>
                    </div>
                    `;
                });

                htmlContent += `</div>`;
                div.innerHTML = htmlContent;

                // Hentikan propagasi scroll peta saat nge-scroll box legenda
                L.DomEvent.disableScrollPropagation(div);

                return div;
            };

            clusterLegend.addTo(map);

        });



        function filterHandler() {
            const urlParams = new URLSearchParams(window.location.search);

            // Menangkap parameter dari URL saat halaman dimuat
            const wilayahParam = urlParams.get('wilayah') || 'all';
            const kecamatanParam = urlParams.get('kecamatan') || 'all';
            const kategoriParam = urlParams.get('kategori') || 'all';
            const clusterParam = urlParams.get('cluster') || 'all';

            const reverseCategoryMap = {
                'makanan_berat': 'Makanan Berat',
                'makanan_khas': 'Makanan Khas',
                'camilan_oleh_oleh': 'Camilan/Oleh-Oleh',
                'minuman': 'Minuman'
            };

            return {
                // Inisialisasi state Alpine berdasarkan URL
                kategori_wilayah: wilayahParam,
                kecamatan: kecamatanParam,
                cluster: clusterParam,
                categories: ['Makanan Berat', 'Camilan/Oleh-Oleh', 'Makanan Khas', 'Minuman'],
                selected: kategoriParam !== 'all' ? reverseCategoryMap[kategoriParam] : 'all',

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
                        'Camilan/Oleh-Oleh': 'camilan_oleh_oleh',
                        'Minuman': 'minuman'
                    };

                    // Menyiapkan nilai untuk URL baru
                    let kategori = this.selected !== 'all' ? categoryMap[this.selected] : 'all';
                    let wilayah = this.kategori_wilayah !== 'all' ? this.kategori_wilayah : 'all';
                    let kecamatan = this.kecamatan !== 'all' ? this.kecamatan : 'all';
                    let cluster = this.cluster !== 'all' ? this.cluster : 'all';

                    // Menyusun query parameter
                    const params = new URLSearchParams({
                        wilayah: wilayah,
                        cluster: cluster,
                        kecamatan: kecamatan,
                        kategori: kategori
                    });

                    // Redirect dengan filter yang sudah diperbarui
                    window.location.href = `/map?${params.toString()}`;
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

        // ===============================
        // GENERATOR WARNA DINAMIS (FILL & OUTLINE)
        // ===============================
        function getClusterStyle(clusterId, kategoriWilayah) {
            // 1. Tangani Noise (Warna Abu-abu solid, tanpa outline khusus)
            if (clusterId === 'noise' || clusterId === null || clusterId === undefined) {
                return {
                    fillColor: "#6b7280",
                    color: "#ffffff",
                    weight: 1
                };
            }

            // 2. Palet Dasar (15 Warna Fill yang kontras dan jelas)
            const baseFills = [
                "#e6194B", "#3cb44b", "#4363d8", "#f58231", "#911eb4",
                "#46f0f0", "#f032e6", "#bcf60c", "#fabebe", "#008080",
                "#e6beff", "#9a6324", "#800000", "#aaffc3", "#808000"
            ];

            // 3. Palet Garis Tepi/Outline (10 Warna kuat untuk memperbanyak kombinasi)
            // Hindari putih/abu terang agar kontras dengan outline marker
            const baseOutlines = [
                "#000000", "#ffffff", "#000075", "#800000", "#004000",
                "#333333", "#4A0404", "#09125C", "#4B4B03", "#3C004F"
            ];

            // 4. Buat string unik (Hash Seed) gabungan dari nama wilayah dan nomor cluster
            // Ini memastikan Cluster 1 Daratan berbeda dengan Cluster 1 Kepulauan
            const seedString = `${kategoriWilayah}_${clusterId}`;

            // Hash sederhana dari string menjadi angka integer
            let hash = 0;
            for (let i = 0; i < seedString.length; i++) {
                hash = seedString.charCodeAt(i) + ((hash << 5) - hash);
            }

            // Pastikan hash positif
            hash = Math.abs(hash);

            // 5. Pilih Fill dan Outline berdasarkan nilai Hash (Total kombinasi: 15 x 10 = 150 variasi unik)
            const fillColor = baseFills[hash % baseFills.length];

            // Karena warna fill banyak, kita pakai outline berbeda setiap siklus fill habis
            const outlineIndex = Math.floor(hash / baseFills.length) % baseOutlines.length;
            const outlineColor = baseOutlines[outlineIndex];

            return {
                fillColor: fillColor,
                color: outlineColor,
                weight: 2 // Outline dibuat sedikit lebih tebal agar variasinya terlihat
            };
        }
    </script>

@endsection
