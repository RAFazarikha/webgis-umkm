@csrf

<div class="grid md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium">Nama Usaha</label>
        <input type="text" name="nama_usaha"
            value="{{ old('nama_usaha', $umkm->nama_usaha ?? '') }}"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

    <div>
        <label>Kategori</label>
        <select name="kategori"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
            <option value="makanan_berat" {{ old('kategori', $umkm->kategori ?? '') == 'makanan_berat' ? 'selected' : '' }}>Makanan Berat</option>
            <option value="camilan_oleh_oleh" {{ old('kategori', $umkm->kategori ?? '') == 'camilan_oleh_oleh' ? 'selected' : '' }}>Camilan / Oleh-oleh</option>
            <option value="makanan_khas" {{ old('kategori', $umkm->kategori ?? '') == 'makanan_khas' ? 'selected' : '' }}>Makanan Khas</option>
            <option value="minuman" {{ old('kategori', $umkm->kategori ?? '') == 'minuman' ? 'selected' : '' }}>Minuman</option>
        </select>
    </div>

    <div>
        <label>Alamat</label>
        <input type="text" name="alamat"
            value="{{ old('alamat', $umkm->alamat ?? '') }}"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

    <div>
        <label>Kecamatan</label>
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
        <label>Latitude</label>
        <input type="text" name="latitude"
            value="{{ old('latitude', $umkm->latitude ?? '') }}"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

    <div>
        <label>Longitude</label>
        <input type="text" name="longitude"
            value="{{ old('longitude', $umkm->longitude ?? '') }}"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
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
        <label>Rating</label>
        <input type="number" step="0.1" min="0" max="5" name="rating"
            value="{{ old('rating', $umkm->rating ?? '') }}"
            placeholder="Misal: 4.5"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

    <div>
        <label>Jumlah Ulasan</label>
        <input type="number" min="0" name="jumlah_ulasan"
            value="{{ old('jumlah_ulasan', $umkm->jumlah_ulasan ?? '') }}"
            placeholder="Misal: 150"
            class="w-full border border-gray-200 shadow-sm rounded-lg px-4 py-2">
    </div>

</div>

<div class="mt-6">
    <button class="px-6 py-3 bg-[#D92D20] text-white rounded-lg">
        Simpan
    </button>
</div>
