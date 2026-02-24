@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')

@php
    $totalUmkm = \App\Models\Umkm::count();
    $totalCluster = \App\Models\Umkm::whereNotNull('cluster_id')->distinct('cluster_id')->count('cluster_id');
    $totalNoise = \App\Models\Umkm::where('is_noise', true)->count();
    $avgRating = round(\App\Models\Umkm::avg('rating'),1);
@endphp

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
        <p class="text-sm text-gray-500 mb-2">Total UMKM</p>
        <h2 class="text-3xl font-bold text-[#111827]">
            {{ $totalUmkm }}
        </h2>
    </div>

    <!-- Cluster -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Total Cluster</p>
        <h2 class="text-3xl font-bold text-[#F59E0B]">
            {{ $totalCluster }}
        </h2>
    </div>

    <!-- Noise -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500 mb-2">Noise Point</p>
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
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

    <h2 class="text-xl font-semibold text-[#111827] mb-6">
        Manajemen Data UMKM
    </h2>

    <div class="flex gap-4">

        <a href="{{ route('admin.umkm.index') }}"
           class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-[#F59E0B] transition">
            Lihat Data UMKM
        </a>

        <a href="{{ route('admin.umkm.create') }}"
           class="px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
            Tambah UMKM
        </a>

    </div>

</div>

@endsection
