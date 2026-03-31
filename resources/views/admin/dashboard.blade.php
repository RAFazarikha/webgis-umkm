@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')

@php
    $response = session('response');
    $best = $response['best_parameter'] ?? null;
@endphp

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@elseif(session('error'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
@endif

<div class="mb-10">
    <h1 class="text-3xl font-bold text-[#111827] mb-2">
        Dashboard Admin
    </h1>
    <p class="text-gray-500">
        Overview sistem WebGIS UMKM Jelajah Rasa.
    </p>
</div>

<!-- STAT CARDS -->
<div class="grid md:grid-cols-4 gap-6 mb-12">

    <!-- Total UMKM -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Jumlah UMKM</p>
        <h2 class="text-3xl font-bold text-[#111827]">
            {{ $totalUmkm }}
        </h2>
    </div>

    <!-- Cluster -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Jumlah Cluster</p>
        <h2 class="text-3xl font-bold text-[#F59E0B]">
            {{ $totalCluster }}
        </h2>
    </div>

    <!-- Noise -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Jumlah Noise</p>
        <h2 class="text-3xl font-bold text-[#D92D20]">
            {{ $totalNoise }}
        </h2>
    </div>

    <!-- Rating -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Rata-rata Rating</p>
        <h2 class="text-3xl font-bold text-[#111827]">
            {{ $avgRating ?? 0 }}
        </h2>
    </div>

</div>

<!-- QUICK ACTION -->
<div x-data="umkmModal()" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-12">

    <h2 class="text-xl font-semibold text-[#111827] mb-6">
        Manajemen Data UMKM
    </h2>

    <div class="flex gap-4">

        <button
            @click="openModal('grid')"
            class="px-6 py-3 bg-[#F59E0B] text-white rounded-lg hover:bg-[#D92D20] transition">
            Optimasi Parameter
        </button>

        <button
            @click="openModal('cluster')"
            class="px-6 py-3 bg-[#F59E0B] text-white rounded-lg hover:bg-[#D92D20] transition">
            Clusterisasi UMKM
        </button>

        <a href="{{ route('admin.umkm.index') }}"
            class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-[#F59E0B] transition">
            Lihat Data UMKM
        </a>

        <a href="{{ route('admin.umkm.create') }}"
            class="px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
            Tambah UMKM
        </a>

    </div>

    @include('components.form-umkm-modal')

</div>

@if ($response)
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

    <h2 class="text-lg font-semibold text-gray-800 mb-4">
        Hasil Uji Coba Parameter DBSCAN
    </h2>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">

            <thead class="bg-gray-100">
                <tr class="text-left text-sm text-gray-600">
                    <th class="px-4 py-3 border">No</th>
                    <th class="px-4 py-3 border">EPS (km)</th>
                    <th class="px-4 py-3 border">MinPts</th>
                    <th class="px-4 py-3 border">Jumlah Cluster</th>
                    <th class="px-4 py-3 border">Noise</th>
                    <th class="px-4 py-3 border">Silhouette Score</th>
                </tr>
            </thead>

            <tbody class="text-sm text-gray-700">

                @foreach($response['results'] as $index => $row)

                @php
                    $isBest = $best &&
                        $row['eps_km'] == $best['eps_km'] &&
                        $row['min_samples'] == $best['min_samples'];
                @endphp

                <tr class="{{ $isBest ? 'bg-yellow-100 border-yellow-400 font-semibold' : 'hover:bg-gray-50' }}">

                    <td class="px-4 py-2 border">
                        {{ $index + 1 }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $row['eps_km'] }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $row['min_samples'] }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $row['jumlah_cluster'] ?? '-' }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $row['jumlah_noise'] ?? '-' }}
                    </td>

                    <td class="px-4 py-2 border">
                        {{ $row['silhouette_coefficient'] ?? '-' }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>
    </div>

</div>
@endif

<script>
    function umkmModal() {
        return {

            showModal: false,
            title: '',
            actionUrl: '',
            mode: '',

            openModal(type) {

                this.showModal = true
                this.mode = type

                if(type === 'grid') {
                    this.title = 'Optimasi Parameter DBSCAN'
                    this.actionUrl = "{{ route('admin.umkm.grid-search') }}"
                }

                if(type === 'cluster') {
                    this.title = 'Clusterisasi UMKM'
                    this.actionUrl = "{{ route('admin.umkm.clustering') }}"
                }

            },

            closeModal() {
                this.showModal = false
            }
        }
    }
</script>

@endsection
