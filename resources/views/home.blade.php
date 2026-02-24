@extends('layouts.app')
@section('title', 'Beranda - Jelajah Rasa')
@section('content')
<x-hero />

<section class="max-w-7xl mx-auto px-6 py-16 text-center">
    <h2 class="text-3xl font-bold text-[#111827] mb-4">Popular Culinary</h2>
    <p class="text-gray-500 mb-6">Check out our most loved culinary spots!</p>
    <a href="/kuliner" class="px-6 py-3 bg-black text-white rounded-lg hover:bg-[#F59E0B] transition">View All</a>

    <div class="grid md:grid-cols-3 gap-8 mt-12">
        <x-culinary-card :tags="['Spicy']" />
        <x-culinary-card :tags="['Fresh']" />
        <x-culinary-card :tags="['Tasty']" />
    </div>
</section>
@endsection
