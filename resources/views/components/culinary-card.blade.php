@props(['title' => 'Kaldu Kokot', 'location' => 'Sumenep', 'tags' => []])
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition">
    <div class="w-full h-40 bg-gray-200 rounded-lg mb-4"></div>
    <h3 class="text-lg font-semibold text-[#111827]">{{ $title }}</h3>
    <p class="text-sm text-gray-500 mb-3">The kaldu kokot in town.</p>
    <div class="flex gap-2 flex-wrap">
        <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">{{ $location }}</span>
        @foreach($tags as $tag)
            <span class="text-xs px-3 py-1 rounded-full bg-yellow-100 text-[#F59E0B]">{{ $tag }}</span>
        @endforeach
    </div>
</div>
