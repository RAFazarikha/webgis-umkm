@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')

    @php
        $response = session('response');
        $best = $response['best_parameter'] ?? null;

        // Menangkap session dari hasil k-distance
        $kDistanceResponse = session('k_distance_response');
    @endphp

    @if (session('success'))
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
            Overview sistem WebGIS UMKM | Peta Kuliner Sumenep.
        </p>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mb-12">

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Keseluruhan UMKM</p>
                    <h2 class="text-3xl font-bold text-[#111827]">
                        {{ $totalUmkm }}
                    </h2>
                </div>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-400">Daratan Utama</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $umkmDaratan }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Kepulauan</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $umkmKepulauan }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Cluster: Semua Wilayah (Gabungan)</p>
                    <h2 class="text-3xl font-bold text-[#F59E0B]">
                        {{ $totalCluster }}
                    </h2>
                </div>
                <span class="p-2 bg-yellow-50 text-yellow-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-400">Daratan (Terpisah)</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $clusterDaratan }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Kepulauan (Terpisah)</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $clusterKepulauan }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Noise: Semua Wilayah (Gabungan)</p>
                    <h2 class="text-3xl font-bold text-[#D92D20]">
                        {{ $totalNoise }}
                    </h2>
                </div>
                <span class="p-2 bg-red-50 text-red-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-400">Daratan (Terpisah)</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $noiseDaratan }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Kepulauan (Terpisah)</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $noiseKepulauan }}</p>
                </div>
            </div>
        </div>

    </div>
    <div x-data="umkmModal()" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-12">
        <h2 class="text-xl font-semibold text-[#111827] mb-6">Manajemen Data UMKM</h2>
        <div class="flex flex-wrap gap-4">
            <button @click="openModal('k_distance')"
                class="px-6 py-3 bg-[#111827] text-white rounded-lg hover:bg-gray-800 transition shadow-sm">Uji Coba
                K-Distance</button>
            <button @click="openModal('grid')"
                class="px-6 py-3 bg-[#F59E0B] text-white rounded-lg hover:bg-yellow-600 transition shadow-sm">Optimasi
                Parameter</button>
            <button @click="openModal('cluster')"
                class="px-6 py-3 bg-[#F59E0B] text-white rounded-lg hover:bg-yellow-600 transition shadow-sm">Clusterisasi
                UMKM</button>
            <a href="{{ route('admin.umkm.index') }}"
                class="px-6 py-3 bg-white text-[#111827] rounded-lg hover:bg-gray-50 border border-gray-300 transition shadow-sm">Lihat
                Data UMKM</a>
            <a href="{{ route('admin.umkm.create') }}"
                class="px-6 py-3 bg-[#D92D20] text-white rounded-lg hover:bg-red-700 transition shadow-sm">Tambah UMKM</a>
        </div>
        @include('components.form-umkm-modal')
    </div>

    @if ($kDistanceResponse)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-12">

            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-[#111827]">
                    Hasil Analisis K-Distance Graph & Evaluasi DBSCAN
                </h2>
                <div class="space-x-2">
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-full">
                        Total Data: {{ $kDistanceResponse['total_data_diproses'] }}
                    </span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                        K Range: {{ $kDistanceResponse['k_range'][0] }} - {{ $kDistanceResponse['k_range'][1] }}
                    </span>
                </div>
            </div>

            <div class="relative h-[400px] w-full mb-8 border border-gray-100 rounded-xl p-4 bg-gray-50/50">
                <canvas id="kDistanceChart"></canvas>
            </div>

            <div class="overflow-x-auto">
                @php
                    // Cari nilai DBI terendah (terbaik) dari seluruh hasil uji coba K
                    $bestDBIScore = collect($kDistanceResponse['results'])
                        ->pluck('evaluasi_dbscan_optimal.davies_bouldin_index')
                        ->filter(function ($val) {
                            return !is_null($val);
                        })
                        ->min(); // Gunakan min() karena nilai DBI kecil = lebih baik
                @endphp

                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr class="text-center text-sm text-[#111827] font-semibold border-b">
                            <th class="px-4 py-3 border-r">K (MinPts)</th>
                            <th class="px-4 py-3 border-r">EPS Terbaik (km)</th>
                            <th class="px-4 py-3 border-r">Titik Belok</th>
                            <th class="px-4 py-3 border-r">Cluster</th>
                            <th class="px-4 py-3 border-r">Core/Border</th>
                            <th class="px-4 py-3 border-r text-[#D92D20]">Noise</th>
                            <th class="px-4 py-3 text-blue-600">DBI (Optimal)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        @foreach ($kDistanceResponse['results'] as $row)
                            @php
                                $evalOptimal = $row['evaluasi_dbscan_optimal'] ?? null;
                                $currentDBI = $evalOptimal['davies_bouldin_index'] ?? null;
                                $isBestK = $currentDBI !== null && $currentDBI == $bestDBIScore;
                            @endphp

                            <tr class="hover:bg-gray-50 transition border-b">
                                <td class="px-4 py-3 border-r font-medium text-center">
                                    {{ $row['parameter_k'] }}
                                </td>
                                <td class="px-4 py-3 border-r text-center font-bold">
                                    @if (isset($evalOptimal['eps_km_digunakan']))
                                        <span class="text-[#F59E0B]">{{ $evalOptimal['eps_km_digunakan'] }}</span>

                                        {{-- Opsional: Menampilkan EPS Elbow asli di bawahnya sebagai perbandingan dengan ukuran kecil --}}
                                        @if ($evalOptimal['eps_km_digunakan'] != $row['rekomendasi_epsilon_km'])
                                            <br><span class="text-[10px] text-gray-400 font-normal">Elbow:
                                                {{ $row['rekomendasi_epsilon_km'] }}</span>
                                        @endif
                                    @elseif(isset($row['rekomendasi_epsilon_km']))
                                        <span class="text-[#F59E0B]">{{ $row['rekomendasi_epsilon_km'] }}</span>
                                    @else
                                        <span class="text-red-500 text-xs font-normal">
                                            Error: {{ $row['error'] ?? 'Unknown Exception' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 border-r text-center text-gray-500">
                                    {{ $row['indeks_elbow'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 border-r text-center font-bold">
                                    {{ $evalOptimal['jumlah_cluster'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 border-r text-center text-gray-500 text-xs">
                                    {{ $evalOptimal['jumlah_core'] ?? '0' }} / {{ $evalOptimal['jumlah_border'] ?? '0' }}
                                </td>
                                <td class="px-4 py-3 border-r text-center text-[#D92D20]">
                                    {{ $evalOptimal['jumlah_noise'] ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center font-medium bg-blue-50/30">
                                    {{ $currentDBI ? number_format($currentDBI, 4) : '-' }}

                                    @if ($isBestK)
                                        <span
                                            class="block mt-1 px-2 py-0.5 text-[10px] bg-blue-500 text-white rounded-full mx-auto w-max">
                                            DBI Terbaik
                                        </span>
                                    @endif
                                </td>
                            </tr>

                            @if (isset($row['evaluasi_kandidat_eps']) && count($row['evaluasi_kandidat_eps']) > 0)
                                <tr>
                                    <td colspan="7" class="p-0 border-b border-gray-300">
                                        <div
                                            class="bg-gray-50/80 p-4 border-l-4 border-indigo-400 ml-4 mb-2 mt-2 rounded-r-lg">
                                            <p class="text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">
                                                📊 Simulasi Kenaikan EPS untuk MinPts = {{ $row['parameter_k'] }}
                                            </p>
                                            <div class="overflow-x-auto">
                                                <table
                                                    class="w-full text-xs text-left bg-white border border-gray-200 rounded">
                                                    <thead class="bg-indigo-50 text-indigo-800">
                                                        <tr>
                                                            <th class="py-2 px-3 border-b">Kenaikan EPS</th>
                                                            <th class="py-2 px-3 border-b">Nilai EPS (km)</th>
                                                            <th class="py-2 px-3 border-b">Cluster</th>
                                                            <th class="py-2 px-3 border-b">Noise</th>
                                                            <th class="py-2 px-3 border-b">% Noise</th>
                                                            <th class="py-2 px-3 border-b">DBI</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($row['evaluasi_kandidat_eps'] as $eps)
                                                            <tr class="hover:bg-gray-50 border-b last:border-0">
                                                                <td
                                                                    class="py-1 px-3 font-medium {{ $eps['kenaikan_eps'] == '+0%' ? 'text-indigo-600' : '' }}">
                                                                    {{ $eps['kenaikan_eps'] }}
                                                                    {{ $eps['kenaikan_eps'] == '+0%' ? '(Asli)' : '' }}
                                                                </td>
                                                                <td class="py-1 px-3">{{ $eps['eps_km'] }}</td>
                                                                <td class="py-1 px-3 font-semibold">
                                                                    {{ $eps['jumlah_cluster'] }}</td>
                                                                <td class="py-1 px-3 text-red-500">
                                                                    {{ $eps['jumlah_noise'] }}</td>
                                                                <td class="py-1 px-3">{{ $eps['persentase_noise'] }}%</td>
                                                                <td
                                                                    class="py-1 px-3 font-semibold {{ $eps['davies_bouldin_index'] == $currentDBI ? 'text-green-600' : '' }}">
                                                                    {{ $eps['davies_bouldin_index'] ? number_format($eps['davies_bouldin_index'], 4) : '-' }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const kData = @json($kDistanceResponse['results']);
                let maxLen = 0;
                kData.forEach(res => {
                    if (res.k_distance_array && res.k_distance_array.length > maxLen) {
                        maxLen = res.k_distance_array.length;
                    }
                });
                const labels = Array.from({
                    length: maxLen
                }, (_, i) => i + 1);
                const colors = ['#F59E0B', '#3B82F6', '#10B981', '#EF4444', '#8B5CF6'];

                const datasets = kData.filter(res => res.k_distance_array).map((res, index) => {
                    const color = colors[index % colors.length];
                    return {
                        label: 'K=' + res.parameter_k + ' (Eps: ' + res.rekomendasi_epsilon_km + ')',
                        data: res.k_distance_array,
                        borderColor: color,
                        backgroundColor: color,
                        borderWidth: 2,
                        tension: 0.1,
                        pointRadius: res.k_distance_array.map((_, i) => i === res.indeks_elbow ? 6 : 0),
                        pointBackgroundColor: res.k_distance_array.map((_, i) => i === res.indeks_elbow ?
                            '#111827' : 'transparent'),
                        pointBorderColor: res.k_distance_array.map((_, i) => i === res.indeks_elbow ?
                            '#111827' : 'transparent'),
                        pointHoverRadius: 8
                    }
                });

                const ctx = document.getElementById('kDistanceChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            },
                            tooltip: {
                                backgroundColor: '#111827',
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ' ➔ Jarak: ' + context.parsed.y +
                                            ' km';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Urutan Titik Data UMKM',
                                    color: '#6B7280'
                                },
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Jarak ke-K (Kilometer)',
                                    color: '#6B7280'
                                },
                                grid: {
                                    color: '#E5E7EB',
                                    borderDash: [5, 5]
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    @if ($response)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <h2 class="text-lg font-semibold text-[#111827] mb-4">
                Hasil Uji Coba Parameter DBSCAN (Grid Search)
            </h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100">
                        <tr class="text-left text-sm text-[#111827] font-semibold border-b">
                            <th class="px-4 py-3 border-r">No</th>
                            <th class="px-4 py-3 border-r">EPS (km)</th>
                            <th class="px-4 py-3 border-r">MinPts</th>
                            <th class="px-4 py-3 border-r">Jumlah Cluster</th>
                            <th class="px-4 py-3 border-r">Core/Border</th>
                            <th class="px-4 py-3 border-r">Noise</th>
                            <th class="px-4 py-3">DBI</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        @foreach ($response['results'] as $index => $row)
                            @php
                                $isBest =
                                    $best &&
                                    $row['eps_km'] == $best['eps_km'] &&
                                    $row['min_samples'] == $best['min_samples'];
                            @endphp

                            <tr
                                class="{{ $isBest ? 'bg-yellow-50 border-l-4 border-l-[#F59E0B] font-semibold' : 'hover:bg-gray-50 border-b' }} transition">
                                <td class="px-4 py-3 border-r">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 border-r {{ $isBest ? 'text-[#F59E0B]' : '' }}">{{ $row['eps_km'] }}
                                </td>
                                <td class="px-4 py-3 border-r">{{ $row['min_samples'] }}</td>
                                <td class="px-4 py-3 border-r">{{ $row['jumlah_cluster'] ?? '-' }}</td>
                                <td class="px-4 py-3 border-r text-xs text-gray-500">
                                    {{ $row['jumlah_core'] ?? '0' }} / {{ $row['jumlah_border'] ?? '0' }}
                                </td>
                                <td class="px-4 py-3 border-r {{ $isBest ? 'text-[#D92D20]' : '' }}">
                                    {{ $row['jumlah_noise'] ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    {{ isset($row['davies_bouldin_index']) ? number_format($row['davies_bouldin_index'], 4) : '-' }}
                                    @if ($isBest)
                                        <span
                                            class="ml-2 inline-block px-2 py-0.5 text-[10px] bg-green-500 text-white rounded-full align-middle">Terbaik</span>
                                    @endif
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
                    if (type === 'grid') {
                        this.title = 'Optimasi Parameter DBSCAN (Grid Search)'
                        this.actionUrl = "{{ route('admin.umkm.grid-search') }}"
                    }
                    if (type === 'cluster') {
                        this.title = 'Clusterisasi UMKM'
                        this.actionUrl = "{{ route('admin.umkm.clustering') }}"
                    }
                    if (type === 'k_distance') {
                        this.title = 'Uji Coba Parameter (K-Distance Graph)'
                        this.actionUrl = "{{ route('admin.umkm.k-distance') }}"
                    }
                },
                closeModal() {
                    this.showModal = false
                }
            }
        }
    </script>

@endsection
