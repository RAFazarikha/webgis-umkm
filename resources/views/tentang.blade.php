@extends('layouts.app')
@section('title', 'Tentang - Peta Kuliner Sumenep')

{{-- Menyiapkan variabel bantu agar penulisan lebih rapi --}}
@php
    $description = "Peta Kuliner Sumenep adalah platform yang memetakan berbagai destinasi kuliner di Kabupaten Sumenep, mulai dari makanan khas, makanan berat, minuman, hingga camilan/oleh-oleh. Jelajahi ragam kuliner terbaik di Sumenep dengan sistem pemetaan spasial kami yang mudah digunakan.";

    // Mengecek apakah UMKM punya foto, jika tidak biarkan kosong agar memakai default dari layout
    $imageUrl = asset('images/hero-kuliner.webp');
@endphp

{{-- Mengisi Yield di Layout --}}
@section('meta_description', $description)

@section('meta_image', $imageUrl)

@section('meta_type', 'website')

@section('content')

<section class="max-w-7xl mx-auto px-6 py-16">
    <!-- Hero Tentang -->
    <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
        <div>
            <h1 class="text-4xl font-bold text-[#111827] mb-6">Peta Kuliner Sumenep</h1>
            <p class="text-gray-600 mb-6 leading-relaxed text-justify">
                Temukan esensi kekayaan kuliner lokal dengan mudah. Peta Kuliner Sumenep hadir sebagai platform WebGIS yang memvisualisasikan lokasi dan mengintegrasikan data UMKM kuliner guna mendukung promosi pariwisata gastronomi di Kabupaten Sumenep.
            </p>
            <a href="/kuliner" class="inline-block px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
                Jelajahi Kuliner
            </a>
        </div>
        <div class="w-full h-80 bg-gray-200 rounded-2xl">
            <img src="{{ asset('images/hero-kuliner.webp') }}" alt="Hero Kuliner" class="w-full h-full object-cover rounded-2xl">
        </div>
    </div>

    <!-- Journey Section -->
    <div class="flex flex-col md:flex-row gap-12 items-center mb-20">
        <div class="md:w-2/5">
            <h2 class="text-3xl font-bold text-[#111827] mb-4">Latar Belakang Kami</h2>
            <p class="text-gray-600 leading-relaxed text-justify">
                Platform ini bermula dari kesadaran akan besarnya potensi UMKM kuliner di Sumenep sebagai aset pariwisata budaya. Kami memadukan kekayaan rasa autentik lokal dengan teknologi pemetaan digital agar UMKM tradisional tidak tertinggal dalam ekosistem wisata modern.
            </p>
        </div>
        <div class="md:w-3/5 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row gap-6 items-start">
            <div class="w-full md:w-1/5 flex justify-center items-center rounded-xl">
                <img src="{{ asset('logos/android-chrome-512x512.png') }}" alt="Our Journey" class="w-24 md:w-64 h-auto object-cover rounded-xl">
            </div>
            <div class="md:w-4/5">
                <h3 class="text-lg font-semibold text-[#111827] mb-2">Tentang Peta Kuliner Sumenep</h3>
                <p class="text-gray-500 text-sm leading-relaxed text-justify">
                    Sistem geomapping ini didedikasikan untuk mengatasi rendahnya visibilitas digital UMKM akibat informasi yang selama ini tersebar secara acak. Platform ini tidak hanya menampilkan peta direktori usaha, tetapi juga mengungkap pola persebaran spasial lokasi kuliner secara analitis untuk mempermudah akses informasi bagi wisatawan.
                </p>
            </div>
        </div>
    </div>

    <!-- Why Section -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-[#111827] mb-4">Mengapa Peta Kuliner Sumenep Hadir?</h2>
        <p class="text-gray-500">Solusi inovatif berbasis data spasial untuk pengalaman wisata yang lebih baik.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Item 1 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition flex gap-6">
            <div class="text-5xl font-extrabold text-[#F59E0B]">1</div>
            <div>
                <h4 class="text-lg font-semibold text-[#111827] mb-2">Sentralisasi Informasi</h4>
                <p class="text-gray-600 text-sm">Mengatasi kebingungan wisatawan dengan menyatukan lokasi dan profil usaha dalam satu peta interaktif yang komprehensif.</p>
            </div>
        </div>

        <!-- Item 2 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition flex gap-6">
            <div class="text-5xl font-extrabold text-[#F59E0B]">2</div>
            <div>
                <h4 class="text-lg font-semibold text-[#111827] mb-2">Analisis Pola Persebaran</h4>
                <p class="text-gray-600 text-sm">Analisis clustering (DBSCAN) untuk mengidentifikasi tingkat kepadatan dan konsentrasi wilayah kuliner unggulan.</p>
            </div>
        </div>

        <!-- Item 3 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition flex gap-6">
            <div class="text-5xl font-extrabold text-[#F59E0B]">3</div>
            <div>
                <h4 class="text-lg font-semibold text-[#111827] mb-2">Pemberdayaan UMKM Lokal</h4>
                <p class="text-gray-600 text-sm">Memperluas jangkauan promosi digital bagi pelaku usaha kuliner tanpa menuntut pemahaman teknologi atau biaya yang tinggi.</p>
            </div>
        </div>

        <!-- Item 4 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition flex gap-6">
            <div class="text-5xl font-extrabold text-[#F59E0B]">4</div>
            <div>
                <h4 class="text-lg font-semibold text-[#111827] mb-2">Kemudahan Navigasi Wisata</h4>
                <p class="text-gray-600 text-sm">Menyediakan fitur pencarian cerdas, penyaringan ragam kategori kuliner, hingga petunjuk arah rute perjalanan secara real-time.</p>
            </div>
        </div>
    </div>
</section>

@endsection
