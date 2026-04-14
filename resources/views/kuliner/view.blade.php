@extends('layouts.app')
@section('title', $umkm->subdistrict->name . ' - Peta Kuliner Sumenep')
@section('content')

<section class="max-w-7xl mx-auto px-6 py-12">

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
                <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">
                    {{ $umkm->rating ? 'Rating: ' . $umkm->rating : 'Rating: -' }}
                </span>

                <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">
                    {{ $umkm->jam_operasional ? 'Jam Operasional: ' . $umkm->jam_operasional : 'Jam Operasional: -' }}
                </span>

                @if ($umkm->kategori == 'makanan_khas')
                    <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">
                        Makanan Khas
                    </span>
                @elseif ($umkm->kategori == 'makanan_berat')
                    <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">
                        Makanan Berat
                    </span>
                @elseif ($umkm->kategori == 'minuman')
                    <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">
                        Minuman
                    </span>
                @else
                    <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">
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
                    <span class="font-semibold text-[#111827]">Kelompok Cluster:</span>
                    <span class="text-gray-600">{{ $umkm->clusterResultAll->first->cluster->cluster ?? 'Noise' }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex gap-4 flex-wrap">
                <a href="https://www.google.com/maps?q={{ $umkm->latitude }},{{ $umkm->longitude }}"
                    target="_blank"
                    class="px-6 py-3 bg-[#F59E0B] text-white rounded-lg hover:bg-yellow-700 transition">
                    Lihat di Google Maps
                </a>

                <a href="{{ route('map') }}?search={{ $umkm->nama_usaha }}"
                    class="px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
                    Lihat di Peta
                </a>

                <a href="{{ url()->previous() }}"
                    class="px-6 py-3 border border-[#111827] text-[#111827] rounded-lg hover:bg-[#111827] hover:text-white transition">
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Map Preview -->
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-[#111827] mb-6">Lokasi</h2>
        <div id="detailMap" class="w-full h-[600px] rounded-2xl border border-gray-200 shadow-sm z-10"></div>
    </div>

</section>

<!-- Leaflet CDN -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const lat = {{ $umkm->latitude ?? -7.0049 }};
        const lng = {{ $umkm->longitude ?? 113.8595 }};

        let userLocation = null;
        let routingControl = null;

        const map = L.map('detailMap').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // =========================
        // AMBIL LOKASI USER
        // =========================
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {

                userLocation = [
                    position.coords.latitude,
                    position.coords.longitude
                ];

                // marker lokasi user
                L.marker(userLocation)
                    .addTo(map)
                    .bindPopup("Lokasi Anda")
                    .openPopup();

                // ✅ AUTO ROUTE SAAT LOKASI SUDAH DAPAT
                showRoute([lat, lng]);

            }, function(error) {
                console.log("Gagal ambil lokasi:", error.message);
            });
        }

        // =========================
        // FUNGSI ROUTING
        // =========================
        function showRoute(destinationLatLng) {

            if (!userLocation) {
                showMapWarning("Tunggu, lokasi Anda sedang diambil...");
                return;
            }

            // hapus rute sebelumnya
            if (routingControl) {
                map.removeControl(routingControl);
            }

            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(userLocation[0], userLocation[1]),
                    L.latLng(destinationLatLng[0], destinationLatLng[1])
                ],
                router: L.Routing.osrmv1({
                    serviceUrl: 'https://router.project-osrm.org/route/v1'
                }),
                lineOptions: {
                    styles: [{ color: '#4285F4', weight: 5, opacity: 0.9 }]
                },
                showAlternatives: true,
                altLineOptions: {
                    styles: [{ color: '#BCCAFA', weight: 5, opacity: 0.8 }]
                },
                routeWhileDragging: false,
                addWaypoints: false,
                draggableWaypoints: false,
                fitSelectedRoutes: true
            }).addTo(map);

            // ✅ TANGKAP ERROR DAN TAMPILKAN DI KANAN ATAS PETA
            routingControl.on('routingerror', function(e) {
                showMapWarning("Lokasi tujuan mungkin berada di pulau berbeda atau tidak memiliki akses jalur darat.");

                // Hapus routing control yang gagal
                map.removeControl(routingControl);
                routingControl = null;

                // ✅ FITUR BARU: Arahkan peta kembali ke lokasi tujuan (UMKM)
                map.flyTo([lat, lng], 15, {
                    animate: true,
                    duration: 1.5 // Durasi animasi pergeseran peta dalam detik
                });

                // ✅ Buka kembali popup marker tujuan agar terlihat jelas
                if (typeof destinationMarker !== 'undefined') {
                    destinationMarker.openPopup();
                }
            });
        }

        // =========================
        // MARKER TUJUAN
        // =========================
        const redIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
            shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Simpan marker ke dalam variabel global (di dalam scope DOMContentLoaded) agar bisa dipanggil saat error
        const destinationMarker = L.marker([lat, lng], { icon: redIcon })
            .addTo(map)
            .bindPopup(`
                <strong class="capitalize">{{ $umkm->nama_usaha }}</strong><br>
                <button id="btnRoute" style="margin-top:5px; padding:4px 8px; background:#2563eb; color:white; border:none; border-radius:4px; cursor:pointer;">
                    Tampilkan Rute
                </button>
            `)
            .openPopup();

        destinationMarker.on('click', function () {
            showRoute([lat, lng]);
        });

        map.on('popupopen', function () {
            const btn = document.getElementById('btnRoute');
            if (btn) {
                btn.addEventListener('click', function () {
                    showRoute([lat, lng]);
                });
            }
        });

        // =========================
        // FUNGSI MENAMPILKAN WARNING DI PETA (KANAN ATAS)
        // =========================
        let warningControl = null;

        function showMapWarning(message) {
            if (warningControl) {
                map.removeControl(warningControl);
            }

            const WarningControl = L.Control.extend({
                options: { position: 'topright' },
                onAdd: function () {
                    const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');

                    container.style.backgroundColor = '#fef2f2';
                    container.style.color = '#991b1b';
                    container.style.padding = '12px 16px';
                    container.style.border = '1px solid #f87171';
                    container.style.borderRadius = '8px';
                    container.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
                    container.style.maxWidth = '300px';
                    container.style.fontSize = '14px';
                    container.style.lineHeight = '1.4';
                    container.style.zIndex = '1000';

                    container.innerHTML = `
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                            <div>
                                <strong style="display: block; margin-bottom: 4px; font-size: 15px;">⚠️ Rute Tidak Tersedia</strong>
                                <span>${message}</span>
                            </div>
                            <button id="closeWarningBtn" style="background:transparent; border:none; color:#991b1b; font-size:20px; cursor:pointer; padding:0; line-height:1; font-weight:bold;">&times;</button>
                        </div>
                    `;

                    L.DomEvent.disableClickPropagation(container);

                    return container;
                }
            });

            warningControl = new WarningControl();
            map.addControl(warningControl);

            setTimeout(() => {
                const closeBtn = document.getElementById('closeWarningBtn');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        map.removeControl(warningControl);
                        warningControl = null;
                    });
                }
            }, 100);

            setTimeout(() => {
                if (warningControl) {
                    map.removeControl(warningControl);
                    warningControl = null;
                }
            }, 7000);
        }

    });
</script>

@endsection
