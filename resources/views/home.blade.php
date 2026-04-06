@extends('layouts.app')
@section('title', 'Beranda - Peta Kuliner Sumenep')
@section('content')
<x-hero />

<section class="max-w-7xl mx-auto px-6 py-16 text-center">
    <h2 class="text-3xl font-bold text-[#111827] mb-4">Popular Culinary</h2>
    <p class="text-gray-500 mb-6">Check out our most loved culinary spots!</p>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-8 mb-6 mx-auto text-center items-center justify-center">
        @foreach ($topumkm as $umkm)
            <x-culinary-card :id="$umkm->id" :title="$umkm->nama_usaha" :location="$umkm->subdistrict->name" :alamat="$umkm->alamat" :kategori="$umkm->kategori" :tags="['Rating: ' . ($umkm->rating ?? '-'), 'Jam Operasional: ' . ($umkm->jam_operasional ?? '-')]" />
        @endforeach
    </div>

    <a href="/kuliner" class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-white border hover:border-[#111827] hover:text-[#111827] transition">View All</a>
</section>
@endsection
