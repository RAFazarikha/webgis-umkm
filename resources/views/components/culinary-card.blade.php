@props(['id' => '1', 'title' => 'Kaldu Kokot', 'location' => 'Sumenep', 'alamat' => 'Jl. Sumenep', 'kategori' => 'makanan_berat', 'tags' => ['Spicy', 'Tasty']])
<div class="w-80 md:w-96 lg:w-full bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition">
    <a href="{{ route('kuliner.view', $id) }}">
    <div class="w-full h-40 bg-white rounded-lg mb-4 text-center justify-center flex items-center">
        @if ($kategori == 'makanan_khas')
            <img class="h-40 object-cover rounded-lg" src="{{ asset('images/makanan-khas.webp') }}" alt="">
        @elseif ($kategori == 'makanan_berat')
            <img class="h-40 object-cover rounded-lg" src="{{ asset('images/makanan-berat.webp') }}" alt="">
        @elseif ($kategori == 'minuman')
            <img class="h-40 object-cover rounded-lg" src="{{ asset('images/minuman.webp') }}" alt="">
        @else
            <img class="h-40 object-cover rounded-lg" src="{{ asset('images/camilan.webp') }}" alt="">
        @endif
    </div>
    </a>
    <a href="{{ route('kuliner.view', $id) }}" class="text-lg font-semibold text-[#111827] capitalize">{{ $title }}</a>
    <p class="text-sm text-gray-500 mb-3 truncate">{{ $alamat }}</p>
    <div class="grid grid-cols-1 gap-2 text-center items-center justify-center">
        <div class="flex gap-2 flex-wrap mx-auto">
            @foreach($tags as $tag)
                <span class="text-xs px-3 py-1 rounded-full bg-red-100 text-[#D92D20]">{{ $tag }}</span>
            @endforeach
        </div>
        <div class="flex gap-2 flex-wrap mx-auto">
            @if ($kategori == 'makanan_khas')
                <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">Makanan Khas</span>
            @elseif ($kategori == 'makanan_berat')
                <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">Makanan Berat</span>
            @elseif ($kategori == 'minuman')
                <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">Minuman</span>
            @else
                <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">Camilan/Oleh-oleh</span>
            @endif

            <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">{{ $location }}</span>
        </div>
    </div>
</div>
