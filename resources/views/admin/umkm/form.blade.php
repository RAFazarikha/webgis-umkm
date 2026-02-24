@csrf

<div class="grid md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium">Nama Usaha</label>
        <input type="text" name="nama_usaha"
            value="{{ old('nama_usaha', $umkm->nama_usaha ?? '') }}"
            class="w-full border rounded-lg px-4 py-2">
    </div>

    <div>
        <label>Kategori</label>
        <select name="kategori"
            class="w-full border rounded-lg px-4 py-2">
            <option value="makanan_berat">Makanan Berat</option>
            <option value="camilan_oleh_oleh">Camilan / Oleh-oleh</option>
            <option value="makanan_khas">Makanan Khas</option>
            <option value="minuman">Minuman</option>
        </select>
    </div>

    <div>
        <label>Alamat</label>
        <input type="text" name="alamat"
            value="{{ old('alamat', $umkm->alamat ?? '') }}"
            class="w-full border rounded-lg px-4 py-2">
    </div>

    <div>
        <label>Kecamatan</label>
        <input type="text" name="kecamatan"
            value="{{ old('kecamatan', $umkm->kecamatan ?? '') }}"
            class="w-full border rounded-lg px-4 py-2">
    </div>

    <div>
        <label>Latitude</label>
        <input type="text" name="latitude"
            value="{{ old('latitude', $umkm->latitude ?? '') }}"
            class="w-full border rounded-lg px-4 py-2">
    </div>

    <div>
        <label>Longitude</label>
        <input type="text" name="longitude"
            value="{{ old('longitude', $umkm->longitude ?? '') }}"
            class="w-full border rounded-lg px-4 py-2">
    </div>

</div>

<div class="mt-6">
    <button class="px-6 py-3 bg-[#D92D20] text-white rounded-lg">
        Simpan
    </button>
</div>
