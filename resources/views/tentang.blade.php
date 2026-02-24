@extends('layouts.app')
@section('title', 'Tentang - Jelajah Rasa')
@section('content')

<section class="max-w-7xl mx-auto px-6 py-16">
    <!-- Hero Tentang -->
    <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
        <div>
            <h1 class="text-4xl font-bold text-[#111827] mb-6">Tentang Jelajah Rasa</h1>
            <p class="text-gray-600 mb-6 leading-relaxed">
                Discover the essence of our culinary offerings and commitment to quality.
                Jelajah Rasa hadir sebagai platform WebGIS untuk memetakan dan mempromosikan
                ragam kuliner khas Madura, khususnya Sumenep.
            </p>
            <a href="/kuliner" class="inline-block px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition">
                Explore Culinary
            </a>
        </div>
        <div class="w-full h-80 bg-gray-200 rounded-2xl"></div>
    </div>

    <!-- Journey Section -->
    <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
        <div>
            <h2 class="text-3xl font-bold text-[#111827] mb-4">Tentang Jelajah Rasa</h2>
            <p class="text-gray-600 leading-relaxed">
                Jelajah Rasa is dedicated to providing structured geographic-based culinary
                information. Platform ini mengintegrasikan sistem informasi geografis
                dengan direktori UMKM kuliner untuk meningkatkan visibilitas usaha lokal.
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex gap-6 items-start">
            <div class="w-20 h-20 bg-gray-200 rounded-xl"></div>
            <div>
                <h3 class="text-lg font-semibold text-[#111827] mb-2">Our Journey</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Jelajah Rasa started with a passion for authentic flavors,
                    evolving into a culinary journey powered by digital mapping technology.
                </p>
            </div>
        </div>
    </div>

    <!-- Why Section -->
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-[#111827] mb-4">Mengapa Jelajah Rasa Hadir?</h2>
        <p class="text-gray-500">Here are the reasons why we stand out.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Item 1 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition flex gap-6">
            <div class="text-5xl font-extrabold text-[#F59E0B]">1</div>
            <div>
                <h4 class="text-lg font-semibold text-[#111827] mb-2">Authenticity</h4>
                <p class="text-gray-600 text-sm">Authenticity and flavor in every dish mapped accurately through GIS.</p>
            </div>
        </div>

        <!-- Item 2 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition flex gap-6">
            <div class="text-5xl font-extrabold text-[#F59E0B]">2</div>
            <div>
                <h4 class="text-lg font-semibold text-[#111827] mb-2">Quality Commitment</h4>
                <p class="text-gray-600 text-sm">Commitment to quality and freshness supported by curated culinary data.</p>
            </div>
        </div>

        <!-- Item 3 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition flex gap-6">
            <div class="text-5xl font-extrabold text-[#F59E0B]">3</div>
            <div>
                <h4 class="text-lg font-semibold text-[#111827] mb-2">Community Support</h4>
                <p class="text-gray-600 text-sm">Local community support and collaboration with UMKM actors.</p>
            </div>
        </div>

        <!-- Item 4 -->
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition flex gap-6">
            <div class="text-5xl font-extrabold text-[#F59E0B]">4</div>
            <div>
                <h4 class="text-lg font-semibold text-[#111827] mb-2">Innovation</h4>
                <p class="text-gray-600 text-sm">Innovative and exciting culinary experiences integrated with digital maps.</p>
            </div>
        </div>
    </div>
</section>

@endsection
