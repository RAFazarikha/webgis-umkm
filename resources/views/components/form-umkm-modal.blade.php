@props(['kategori' => ['makanan_khas', 'makanan_berat', 'minuman', 'camilan_oleh_oleh']])

<div x-show="showModal" x-transition class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">

    <div class="bg-white rounded-xl w-[500px] p-6">

        <h3 class="text-lg font-semibold mb-4" x-text="title"></h3>

        <form :action="actionUrl" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm mb-1">Kategori Wilayah</label>
                <select name="kategori_wilayah" class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                    <option value="daratan_utama" selected>Daratan Utama</option>
                    <option value="kepulauan">Kepulauan</option>
                    <option value="">Semua Wilayah</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm mb-1">Kecamatan</label>
                <select name="kecamatan" class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($kecamatans as $k)
                        <option value="{{ $k->name }}">{{ $k->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm mb-1">Kategori</label>
                <select name="kategori" class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategori as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4" x-show="mode === 'cluster'">
                <label class="block text-sm mb-1">Eps</label>
                <input type="number" step="0.0001" name="eps" value="0.7"
                    class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
            </div>

            <div class="mb-4" x-show="mode === 'grid'">
                <label class="block text-sm mb-1">Range Eps</label>

                <div class="grid grid-cols-2 gap-3">
                    <input type="number" step="0.1" name="eps_start" placeholder="Eps Min" value="0.2"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">

                    <input type="number" step="0.1" name="eps_end" placeholder="Eps Max" value="1"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                </div>
            </div>

            <div class="mb-4" x-show="mode === 'cluster'">
                <label class="block text-sm mb-1">Min Samples</label>
                <input type="number" step="1" name="min_samples" value="10"
                    class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
            </div>

            <div class="mb-4" x-show="mode === 'grid'">
                <label class="block text-sm mb-1">Range Min Samples</label>

                <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="minpts_start" placeholder="Min" step="1" value="4"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">

                    <input type="number" name="minpts_end" placeholder="Max" step="1" value="10"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                </div>
            </div>

            <div class="mb-4" x-show="mode === 'k_distance'">
                <label class="block text-sm mb-1">Rentang K (MinPts)</label>
                <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="k_start" placeholder="K Min" step="1" value="3"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                    <input type="number" name="k_end" placeholder="K Max" step="1" value="6"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">

                <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-300 rounded-lg">
                    Batal
                </button>

                <button type="submit" class="px-4 py-2 bg-[#F59E0B] text-white rounded-lg">
                    Proses
                </button>

            </div>

        </form>

    </div>

</div>
