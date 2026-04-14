@csrf

<div class="grid md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium">Nama Usaha</label>
        <input type="text" name="nama_usaha"
            value="{{ old('nama_usaha', $umkm->nama_usaha ?? '') }}"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium">Kategori</label>
        <select name="kategori"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
            <option value="makanan_berat" {{ old('kategori', $umkm->kategori ?? '') == 'makanan_berat' ? 'selected' : '' }}>Makanan Berat</option>
            <option value="camilan_oleh_oleh" {{ old('kategori', $umkm->kategori ?? '') == 'camilan_oleh_oleh' ? 'selected' : '' }}>Camilan / Oleh-oleh</option>
            <option value="makanan_khas" {{ old('kategori', $umkm->kategori ?? '') == 'makanan_khas' ? 'selected' : '' }}>Makanan Khas</option>
            <option value="minuman" {{ old('kategori', $umkm->kategori ?? '') == 'minuman' ? 'selected' : '' }}>Minuman</option>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium">Alamat</label>
        <input type="text" name="alamat"
            value="{{ old('alamat', $umkm->alamat ?? '') }}"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium">Kecamatan</label>
        <select name="subdistrict_id" class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
            <option value="" disabled {{ old('subdistrict_id', $umkm->subdistrict_id ?? '') == '' ? 'selected' : '' }}>
                -- Pilih Kecamatan --
            </option>
            @foreach($kecamatan as $k)
                <option value="{{ $k->id }}"
                    {{ old('subdistrict_id', $umkm->subdistrict_id ?? '') == $k->id ? 'selected' : '' }}>
                    {{ $k->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium">Latitude</label>
        <input type="text" name="latitude" id="latitude"
            value="{{ old('latitude', $umkm->latitude ?? '') }}"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2 bg-gray-50" readonly>
    </div>

    <div>
        <label class="block text-sm font-medium">Longitude</label>
        <input type="text" name="longitude" id="longitude"
            value="{{ old('longitude', $umkm->longitude ?? '') }}"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2 bg-gray-50" readonly>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-2">Pilih Lokasi pada Peta</label>
        <div id="map" class="w-full rounded-lg border border-gray-200 shadow-sm" style="height: 400px; z-index: 1;"></div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Jam Operasional</label>
        <div class="flex items-center gap-3">
            <input type="time" name="jam_buka"
                value="{{ old('jam_buka', $jam_buka ?? '') }}"
                class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
            <span class="text-gray-500 font-medium">-</span>
            <input type="time" name="jam_tutup"
                value="{{ old('jam_tutup', $jam_tutup ?? '') }}"
                class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium">Rating</label>
        <input type="number" step="0.1" min="0" max="5" name="rating"
            value="{{ old('rating', $umkm->rating ?? '') }}"
            placeholder="Misal: 4.5"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

    <div>
        <label class="block text-sm font-medium">Jumlah Ulasan</label>
        <input type="number" min="0" name="jumlah_ulasan"
            value="{{ old('jumlah_ulasan', $umkm->jumlah_ulasan ?? '') }}"
            placeholder="Misal: 150"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

</div>

<div class="mt-6">
    <button type="submit" class="px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
        Simpan
    </button>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen input
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        // Koordinat default (Misal: Tengah Indonesia/Jakarta). Silakan ubah sesuai kota Anda.
        let startLat = -7.009551962301057;
        let startLng = 113.85829959281423;
        let zoomLevel = 11;

        // Jika form dalam mode edit (nilai lat & lng sudah ada dari database)
        if (latInput.value && lngInput.value) {
            startLat = parseFloat(latInput.value);
            startLng = parseFloat(lngInput.value);
            zoomLevel = 16; // Zoom lebih dekat jika lokasi sudah ada
        }

        // Inisialisasi peta
        const map = L.map('map').setView([startLat, startLng], zoomLevel);

        // Tambahkan layer peta dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker;

        // Jika ada koordinat awal (saat edit), tambahkan marker awal
        if (latInput.value && lngInput.value) {
            marker = L.marker([startLat, startLng]).addTo(map);
        }

        // Event listener ketika peta diklik
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // Masukkan nilai ke input form
            latInput.value = lat;
            lngInput.value = lng;

            // Pindahkan marker atau buat marker baru jika belum ada
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        });
    });
</script>
