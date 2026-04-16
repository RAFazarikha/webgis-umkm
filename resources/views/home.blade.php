@extends('layouts.app')
@section('title', 'Beranda - Peta Kuliner Sumenep')

{{-- Menyiapkan variabel bantu agar penulisan lebih rapi --}}
@php
    $description = "Peta Kuliner Sumenep adalah platform yang memetakan berbagai destinasi kuliner di Kabupaten Sumenep, mulai dari makanan khas, makanan berat, minuman, hingga camilan/oleh-oleh. Jelajahi ragam kuliner terbaik di Sumenep dengan sistem pemetaan spasial kami yang mudah digunakan.";

    // Mengecek apakah UMKM punya foto, jika tidak biarkan kosong agar memakai default dari layout
    $imageUrl = asset('images/hero-kuliner.webp');
@endphp

{{-- Mengisi Yield di Layout --}}
@section('meta_description', $description)

{{-- Hanya mengisi meta_image jika UMKM punya foto --}}
@if($imageUrl)
    @section('meta_image', $imageUrl)
@endif

@section('meta_type', 'website')

@section('content')
<x-hero />

<section class="max-w-7xl mx-auto px-6 py-16 text-center">
    <h2 class="text-3xl font-bold text-[#111827] mb-4">Popular Culinary</h2>
    <p class="text-gray-500 mb-6">Check out our most loved culinary spots!</p>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-8 mb-6 mx-auto text-center items-center justify-center">
        @foreach ($topumkm as $umkm)
            <x-culinary-card :id="$umkm->id" :title="$umkm->nama_usaha" :location="$umkm->subdistrict->name" :alamat="$umkm->alamat" :kategori="$umkm->kategori" :tags="['Rating: ' . ($umkm->rating ?? '-'), 'Jam Operasional: ' . ($umkm->jam_operasional ?? '-')]" :slug="$umkm->slug" />
        @endforeach
    </div>

    <a href="/kuliner" class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-white border hover:border-[#111827] hover:text-[#111827] transition">View All</a>
</section>
@endsection
