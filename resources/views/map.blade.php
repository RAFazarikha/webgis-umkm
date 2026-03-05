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
                <label class="block text-sm font-medium mb-2">Search</label>
                <input type="text" x-model="search"
                    placeholder="Type to search..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F59E0B]" />
                <p class="text-xs text-gray-400 mt-1">Find specific locations.</p>
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

<!-- Leaflet CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/alpinejs" defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('map').setView([-7.0049, 113.8595], 12); // Sumenep center

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

        const locations = [
            @foreach($umkms as $umkm)
            {
                lat: {{ $umkm->latitude }},
                lng: {{ $umkm->longitude }},
                name: "{{ $umkm->nama_usaha }}",
                category: "{{ $umkm->kategori }}",
                district: "{{ $umkm->subdistrict->name }}",
                address: "{{ $umkm->alamat }}",
                open_hours: "{{ $umkm->jam_operasional ?? '-' }}",
                cluster: "{{ $umkm->clusterResultNone->cluster ?? 'Noise' }}",
                detail_url: "{{ route('kuliner.view', $umkm->id) }}"
            },
            @endforeach
        ];

        locations.forEach(loc => {

            L.circleMarker([loc.lat, loc.lng], {
                radius: 3,
                fillColor: getClusterColor(loc.cluster),
                color: "#ffffff",
                weight: 2,
                opacity: 0,
                fillOpacity: 0.9
            })
            .addTo(map)
            .bindTooltip(
                `<b class="capitalize">${loc.name}</b><br>Cluster: ${loc.cluster}`,
                {
                    direction: "top",
                    offset: [0, -5],
                    opacity: 0.9
                }
            )
            .on('click', function () {
                window.dispatchEvent(new CustomEvent('open-sidebar', {
                    detail: loc
                }));
            });

        });
    });

    function filterHandler() {
        return {
            search: '',
            categories: ['Makanan Berat', 'Oleh-Oleh', 'Makanan Khas'],
            selected: [],
            toggle(category) {
                if (this.selected.includes(category)) {
                    this.selected = this.selected.filter(c => c !== category);
                } else {
                    this.selected.push(category);
                }
            },
            applyFilter() {
                console.log('Search:', this.search);
                console.log('Selected:', this.selected);
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
            "#ef4444","#3b82f6","#22c55e","#f59e0b","#8b5cf6",
            "#ec4899","#14b8a6","#eab308","#6366f1","#10b981",
            "#f97316","#06b6d4","#a855f7","#84cc16","#f43f5e",
            "#0ea5e9","#d946ef","#65a30d","#fb923c","#4f46e5",
            "#059669","#dc2626","#9333ea","#16a34a","#0284c7",
            "#be123c","#7c3aed","#15803d","#0f766e","#9a3412"
        ];

        return colors[cluster] ?? "#6b7280";
    }
</script>

@endsection
