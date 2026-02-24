@extends('layouts.admin')
@section('title','Data UMKM')

@section('content')

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-[#111827]">Data UMKM</h1>
    <a href="{{ route('admin.umkm.create') }}"
       class="px-4 py-2 bg-[#D92D20] text-white rounded-lg">
       Tambah UMKM
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white shadow rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="p-4 text-left">Nama</th>
                <th class="text-left">Kategori</th>
                <th class="text-left">Kecamatan</th>
                <th class="text-left">Cluster</th>
                <th class="text-left">Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($umkms as $umkm)
            <tr class="border-t">
                <td class="p-4">{{ $umkm->nama_usaha }}</td>
                <td>{{ $umkm->kategori }}</td>
                <td>{{ $umkm->subdistrict->name ?? '-' }}</td>
                <td>{{ $umkm->cluster_id ?? '-' }}</td>
                <td class="space-x-2">
                    <a href="{{ route('admin.umkm.edit',$umkm) }}"
                       class="text-blue-600">Edit</a>

                    <form action="{{ route('admin.umkm.destroy',$umkm) }}"
                          method="POST"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $umkms->links() }}
</div>

@endsection
