@props(['kategori' => ['makanan_khas', 'makanan_berat', 'minuman', 'camilan_oleh_oleh']])

<div
    x-show="showModal"
    x-transition
    class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
>

    <div class="bg-white rounded-xl w-[500px] p-6">

        <h3 class="text-lg font-semibold mb-4" x-text="title"></h3>

        <form :action="actionUrl" method="POST">
            @csrf

            <!-- Filter Kecamatan -->
            <div class="mb-4">
                <label class="block text-sm mb-1">Kecamatan</label>
                <select name="kecamatan" class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                    <option value="">Semua Kecamatan</option>
                    @foreach($kecamatans as $k)
                        <option value="{{ $k->name }}">{{ $k->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kategori -->
            <div class="mb-4">
                <label class="block text-sm mb-1">Kategori</label>
                <select name="kategori" class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <!-- EPS untuk clustering -->
            <div class="mb-4" x-show="mode === 'cluster'">
                <label class="block text-sm mb-1">Eps</label>
                <input type="number" step="0.01" name="eps" value="0.7"
                    class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
            </div>

            <!-- EPS range untuk grid search -->
            <div class="mb-4" x-show="mode === 'grid'">
                <label class="block text-sm mb-1">Range Eps</label>

                <div class="grid grid-cols-2 gap-3">
                    <input type="number" step="0.01" name="eps_min" placeholder="Eps Min" value="0.2"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">

                    <input type="number" step="0.01" name="eps_max" placeholder="Eps Max" value="1"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                </div>
            </div>

            <!-- min_samples untuk clustering -->
            <div class="mb-4" x-show="mode === 'cluster'">
                <label class="block text-sm mb-1">Min Samples</label>
                <input type="number" name="min_samples" value="10"
                    class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
            </div>

            <!-- min_samples range untuk grid search -->
            <div class="mb-4" x-show="mode === 'grid'">
                <label class="block text-sm mb-1">Range Min Samples</label>

                <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="min_samples_min" placeholder="Min" value="4"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">

                    <input type="number" name="min_samples_max" placeholder="Max" value="10"
                        class="w-full border border-gray-200 shadow-sm rounded-lg p-2">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">

                <button
                    type="button"
                    @click="closeModal"
                    class="px-4 py-2 bg-gray-300 rounded-lg">
                    Batal
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 bg-[#F59E0B] text-white rounded-lg">
                    Proses
                </button>

            </div>

        </form>

    </div>

</div>
