@extends('layouts.admin')
@section('title', 'Data UMKM')

@section('content')

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-[#111827]">Data UMKM</h1>

        <div class="flex gap-2">
            <form action="{{ route('admin.umkm.export') }}" method="GET" target="_blank">
                <input type="hidden" name="kategori_wilayah" value="{{ request('kategori_wilayah', 'all') }}">
                <input type="hidden" name="kecamatan" value="{{ request('kecamatan', 'all') }}">
                <input type="hidden" name="kategori" value="{{ request('kategori', 'all') }}">
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    Download CSV
                </button>
            </form>

            <a href="{{ route('admin.umkm.create') }}"
                class="px-4 py-2 bg-[#D92D20] hover:bg-red-700 text-white rounded-lg transition">
                Tambah UMKM
            </a>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl shadow mb-6">
        <form action="{{ route('admin.umkm.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">

            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah</label>
                <select name="kategori_wilayah"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all" {{ request('kategori_wilayah') == 'all' ? 'selected' : '' }}>Semua Wilayah
                    </option>
                    <option value="daratan_utama" {{ request('kategori_wilayah') == 'daratan_utama' ? 'selected' : '' }}>
                        Daratan Utama</option>
                    <option value="kepulauan" {{ request('kategori_wilayah') == 'kepulauan' ? 'selected' : '' }}>Kepulauan
                    </option>
                </select>
            </div>

            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                <select name="kecamatan"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">Semua Kecamatan</option>
                    @foreach ($kecamatans as $kec)
                        <option value="{{ $kec->name }}" {{ request('kecamatan') == $kec->name ? 'selected' : '' }}>
                            {{ $kec->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">Semua Kategori</option>
                    <option value="makanan_berat" {{ request('kategori') == 'makanan_berat' ? 'selected' : '' }}>Makanan
                        Berat</option>
                    <option value="makanan_khas" {{ request('kategori') == 'makanan_khas' ? 'selected' : '' }}>Makanan Khas
                    </option>
                    <option value="minuman" {{ request('kategori') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="camilan_oleh_oleh" {{ request('kategori') == 'camilan_oleh_oleh' ? 'selected' : '' }}>
                        Camilan/Oleh-oleh</option>
                </select>
            </div>

            <div class="w-full md:w-1/4 flex gap-2">
                <button type="submit"
                    class="w-full px-6 py-2 text-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Filter
                </button>
                <a href="{{ route('admin.umkm.index') }}"
                    class="w-full px-6 py-2 text-center bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-xl overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="p-4 text-left font-semibold text-gray-600">Nama</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Kategori</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Kecamatan</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Cluster</th>
                    <th class="p-4 text-left font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($umkms as $umkm)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">{{ $umkm->nama_usaha }}</td>
                        <td class="p-4">{{ $umkm->kategori }}</td>
                        <td class="p-4">{{ $umkm->subdistrict->name ?? '-' }}</td>
                        <td class="p-4">
                            @php
                                $cluster = optional($umkm->clusterResultAll->first())->cluster;
                            @endphp
                            @if (is_null($cluster))
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">Noise</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">Cluster
                                    {{ $cluster }}</span>
                            @endif
                        </td>
                        <td class="p-4 space-x-2">
                            <a href="{{ route('admin.umkm.edit', $umkm) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('admin.umkm.destroy', $umkm) }}" method="POST" class="inline"
                                onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            Tidak ada data UMKM yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $umkms->links() }}
    </div>

@endsection
