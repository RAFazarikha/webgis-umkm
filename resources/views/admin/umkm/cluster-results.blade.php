@extends('layouts.admin')
@section('title', 'Detail Hasil Cluster')

@section('content')

    <div class="mb-10 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-[#111827] mb-2">
                Hasil Clusterisasi
            </h1>
            <p class="text-gray-500">
                Rincian distribusi UMKM pada setiap cluster di Daratan Utama dan Kepulauan.
            </p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8 mb-12">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-xl font-bold text-[#111827] mb-4">Daratan Utama</h2>

                <div class="flex flex-wrap gap-3">
                    <span
                        class="px-3 py-1 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg shadow-sm">
                        EPS: <span class="text-[#F59E0B]">{{ $summaryDaratan->eps ?? '-' }}</span>
                    </span>
                    <span
                        class="px-3 py-1 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg shadow-sm">
                        MinPts: <span class="text-[#F59E0B]">{{ $summaryDaratan->min_samples ?? '-' }}</span>
                    </span>
                    <span
                        class="px-3 py-1 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg shadow-sm">
                        Silhouette: <span
                            class="text-[#10B981]">{{ isset($summaryDaratan->silhouette_score) ? number_format($summaryDaratan->silhouette_score, 4) : '-' }}</span>
                    </span>
                </div>
            </div>

            <div class="p-6 flex-grow">
                @if ($clustersDaratan->isEmpty())
                    <div class="text-center py-10 text-gray-400">
                        Belum ada data cluster. Silakan jalankan proses clusterisasi untuk wilayah Daratan Utama.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr class="text-left text-sm text-[#111827] font-semibold">
                                    <th class="px-4 py-3 border-b">Nama Cluster</th>
                                    <th class="px-4 py-3 border-b text-center">Jumlah UMKM</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700">
                                @foreach ($clustersDaratan as $row)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 border-b font-medium">
                                            @if ($row->is_noise)
                                                <span class="text-[#D92D20]">Noise</span>
                                            @else
                                                Cluster {{ $row->cluster }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border-b text-center">
                                            {{ $row->total }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-xl font-bold text-[#111827] mb-4">Kepulauan</h2>

                <div class="flex flex-wrap gap-3">
                    <span
                        class="px-3 py-1 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg shadow-sm">
                        EPS: <span class="text-[#F59E0B]">{{ $summaryKepulauan->eps ?? '-' }}</span>
                    </span>
                    <span
                        class="px-3 py-1 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg shadow-sm">
                        MinPts: <span class="text-[#F59E0B]">{{ $summaryKepulauan->min_samples ?? '-' }}</span>
                    </span>
                    <span
                        class="px-3 py-1 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg shadow-sm">
                        Silhouette: <span
                            class="text-[#10B981]">{{ isset($summaryKepulauan->silhouette_score) ? number_format($summaryKepulauan->silhouette_score, 4) : '-' }}</span>
                    </span>
                </div>
            </div>

            <div class="p-6 flex-grow">
                @if ($clustersKepulauan->isEmpty())
                    <div class="text-center py-10 text-gray-400">
                        Belum ada data cluster. Silakan jalankan proses clusterisasi untuk wilayah Kepulauan.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-100">
                                <tr class="text-left text-sm text-[#111827] font-semibold">
                                    <th class="px-4 py-3 border-b">Nama Cluster</th>
                                    <th class="px-4 py-3 border-b text-center">Jumlah UMKM</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700">
                                @foreach ($clustersKepulauan as $row)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 border-b font-medium">
                                            @if ($row->is_noise)
                                                <span class="text-[#D92D20]">Noise</span>
                                            @else
                                                Cluster {{ $row->cluster }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 border-b text-center">
                                            {{ $row->total }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection
