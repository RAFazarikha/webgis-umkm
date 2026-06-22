<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClusterResult;
use App\Models\Subdistrict;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $kecamatans = Subdistrict::orderBy('name', 'asc')->get();

        // Tangkap parameter (gunakan 'kategori_wilayah' agar sinkron dengan form HTML sebelumnya)
        $wilayah = $request->input('kategori_wilayah', 'all');
        $kecamatan = $request->input('kecamatan', 'all');
        $kategori = $request->input('kategori', 'all');
        $cluster = $request->input('cluster', 'all');
        $search = $request->input('search');

        // 1. Logika Kunci Filter (Single String) diadopsi dari map
        $filterKey = "wil_{$wilayah}_kec_{$kecamatan}_kat_{$kategori}";
        $defaultFilter = 'wil_all_kec_all_kat_all';

        $clusterExists = ClusterResult::where('filter', $filterKey)->exists();
        $activeFilter = $clusterExists ? $filterKey : $defaultFilter;

        // 2. Query Utama Menggunakan when()
        $query = Umkm::with([
            'subdistrict',
            'clusterResultAll' => function ($q) use ($activeFilter) {
                $q->where('filter', $activeFilter);
            },
        ])
            ->when($wilayah !== 'all', function ($q) use ($wilayah) {
                $q->whereHas('subdistrict', function ($sub) use ($wilayah) {
                    $sub->where('kategori_wilayah', $wilayah);
                });
            })
            ->when($kecamatan !== 'all', function ($q) use ($kecamatan) {
                $q->whereHas('subdistrict', function ($sub) use ($kecamatan) {
                    $sub->where('name', $kecamatan);
                });
            })
            ->when($kategori !== 'all', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            })
            ->when($cluster !== 'all', function ($q) use ($cluster, $activeFilter) {
                $q->whereHas('clusterResultAll', function ($q2) use ($cluster, $activeFilter) {
                    $q2->where('filter', $activeFilter);

                    if ($cluster === 'noise') {
                        $q2->where('is_noise', true);
                    } else {
                        $q2->where('cluster', (int) $cluster);
                    }
                });
            })
            ->when($clusterExists, function ($q) use ($activeFilter) {
                $q->whereHas('clusterResultAll', function ($q2) use ($activeFilter) {
                    $q2->where('filter', $activeFilter);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%");
            });

        // 3. Eksekusi query dengan pagination
        $umkms = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Mengambil daftar list cluster untuk form dropdown di tabel index
        $clusters = ClusterResult::where('filter', $defaultFilter)
            ->whereNotNull('cluster')
            ->distinct()
            ->orderBy('cluster', 'asc')
            ->pluck('cluster');

        return view('admin.umkm.index', compact('umkms', 'kecamatans', 'clusters'));
    }

    public function dashboard()
    {
        $totalUmkm = Umkm::count();

        // 1. Hitung UMKM Daratan Utama
        $umkmDaratan = Umkm::whereHas('subdistrict', function ($query) {
            $query->where('kategori_wilayah', 'daratan_utama');
        })->count();

        // 2. Hitung UMKM Kepulauan
        $umkmKepulauan = Umkm::whereHas('subdistrict', function ($query) {
            $query->where('kategori_wilayah', 'kepulauan');
        })->count();

        // 3. Data Keseluruhan / Semua Wilayah (Total Cluster & Total Noise)
        $filterAll = 'wil_all_kec_all_kat_all';
        $totalCluster = ClusterResult::whereNotNull('cluster')
            ->where('filter', $filterAll)
            ->distinct('cluster')
            ->count('cluster');
        $totalNoise = ClusterResult::where('is_noise', true)
            ->where('filter', $filterAll)
            ->count();

        // 4. Data Daratan Utama (Cluster & Noise)
        $filterDaratan = 'wil_daratan_utama_kec_all_kat_all';
        $clusterDaratan = ClusterResult::whereNotNull('cluster')
            ->where('filter', $filterDaratan)
            ->distinct('cluster')
            ->count('cluster');
        $noiseDaratan = ClusterResult::where('is_noise', true)
            ->where('filter', $filterDaratan)
            ->count();

        // 5. Data Kepulauan (Cluster & Noise)
        $filterKepulauan = 'wil_kepulauan_kec_all_kat_all';
        $clusterKepulauan = ClusterResult::whereNotNull('cluster')
            ->where('filter', $filterKepulauan)
            ->distinct('cluster')
            ->count('cluster');
        $noiseKepulauan = ClusterResult::where('is_noise', true)
            ->where('filter', $filterKepulauan)
            ->count();

        $kecamatans = Subdistrict::withCount('umkms')->get();

        return view('admin.dashboard', compact(
            'totalUmkm',
            'umkmDaratan',
            'umkmKepulauan',
            'clusterDaratan',
            'noiseDaratan',
            'clusterKepulauan',
            'noiseKepulauan',
            'totalCluster',
            'totalNoise',
            'kecamatans'
        ));
    }

    public function create()
    {
        $kecamatan = Subdistrict::all();

        return view('admin.umkm.create', compact('kecamatan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'kategori' => 'required',
            'alamat' => 'required',
            'subdistrict_id' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'jam_buka' => 'nullable|date_format:H:i',
            'jam_tutup' => 'nullable|date_format:H:i|after:jam_buka',
            'rating' => 'nullable|numeric|min:0|max:5',
            'jumlah_ulasan' => 'nullable|integer|min:0',
        ]);

        // Menggabungkan "08:00" dan "16:00" menjadi "08:00 - 16:00"
        $jam_operasional = $request->jam_buka.' - '.$request->jam_tutup;

        // Mengubah ":" menjadi "." agar sesuai keinginan Anda menjadi "08.00 - 16.00"
        $jam_operasional = str_replace(':', '.', $jam_operasional);

        Umkm::create([
            'nama_usaha' => $validated['nama_usaha'],
            'kategori' => $validated['kategori'],
            'alamat' => $validated['alamat'],
            'subdistrict_id' => $validated['subdistrict_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'jam_operasional' => $jam_operasional,
            'rating' => $validated['rating'] ?? null,
            'jumlah_ulasan' => $validated['jumlah_ulasan'] ?? null,
        ]);

        return redirect()->route('admin.umkm.index')
            ->with('success', 'UMKM berhasil ditambahkan');
    }

    public function edit(Umkm $umkm)
    {
        $kecamatan = Subdistrict::all();

        $umkm = Umkm::findOrFail($umkm->id);

        // Default value jika jam operasional kosong
        $jam_buka = '';
        $jam_tutup = '';

        // Cek apakah data jam_operasional ada di database
        if ($umkm->jam_operasional) {
            // Pecah string "08.00 - 16.00" menjadi array berdasarkan pemisah " - "
            $pecah_jam = explode(' - ', $umkm->jam_operasional);

            // Pastikan array memiliki 2 elemen untuk menghindari error
            if (count($pecah_jam) == 2) {
                // Kembalikan format "." menjadi ":" agar dikenali oleh input type="time"
                $jam_buka = str_replace('.', ':', $pecah_jam[0]);
                $jam_tutup = str_replace('.', ':', $pecah_jam[1]);
            }
        }

        return view('admin.umkm.edit', compact('umkm', 'kecamatan', 'jam_buka', 'jam_tutup'));
    }

    public function update(Request $request, Umkm $umkm)
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:255',
            'kategori' => 'required',
            'alamat' => 'required',
            'subdistrict_id' => 'required|numeric',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'jam_buka' => 'nullable|date_format:H:i',
            'jam_tutup' => 'nullable|date_format:H:i|after:jam_buka',
            'rating' => 'nullable|numeric|min:0|max:5',
            'jumlah_ulasan' => 'nullable|integer|min:0',
        ]);

        if ($request->jam_buka && $request->jam_tutup) {
            $validated['jam_operasional'] = str_replace(':', '.', $request->jam_buka).' - '.str_replace(':', '.', $request->jam_tutup);
        }

        $umkm->update([
            'nama_usaha' => $validated['nama_usaha'],
            'kategori' => $validated['kategori'],
            'alamat' => $validated['alamat'],
            'subdistrict_id' => $validated['subdistrict_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'jam_operasional' => ($request->jam_buka && $request->jam_tutup) ? str_replace(':', '.', $request->jam_buka).' - '.str_replace(':', '.', $request->jam_tutup) : null,
            'rating' => $validated['rating'] ?? null,
            'jumlah_ulasan' => $validated['jumlah_ulasan'] ?? null,
        ]);

        return redirect()->route('admin.umkm.index')
            ->with('success', 'UMKM berhasil diperbarui');
    }

    public function destroy(Umkm $umkm)
    {
        $umkm->delete();

        return back()->with('success', 'UMKM berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {

            $header = fgetcsv($handle, 1000, ';');

            $expectedHeader = [
                'nama_usaha',
                'kategori',
                'alamat',
                'subdistrict_id',
                'jam_buka',
                'jam_tutup',
                'rating',
                'jumlah_ulasan',
                'latitude',
                'longitude',
                'cluster_id',
                'is_noise',
            ];

            if ($header !== $expectedHeader) {
                return back()->with('error', 'Format header CSV tidak sesuai.');
            }

            $data = [];

            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $data[] = [
                    'nama_usaha' => $row[0],
                    'kategori' => $row[1],
                    'alamat' => $row[2],
                    'subdistrict_id' => (int) $row[3],
                    'jam_operasional' => ($row[4] && $row[5]) ? $row[4].' - '.$row[5] : null,
                    'rating' => $row[6] !== '' ? (float) $row[6] : null,
                    'jumlah_ulasan' => $row[7] !== '' ? (int) $row[7] : null,
                    'latitude' => (float) $row[8],
                    'longitude' => (float) $row[9],
                ];
            }

            fclose($handle);

            // Pakai create() per baris agar trait Sluggable aktif otomatis
            foreach (array_chunk($data, 100) as $chunk) {
                foreach ($chunk as $row) {
                    Umkm::create($row);
                }
            }

            return redirect()->route('admin.umkm.index')
                ->with('success', 'Data CSV berhasil diimport.');
        }

        return back()->with('error', 'File tidak dapat dibaca.');
    }

    public function runClustering(Request $request)
    {
        $request->validate([
            'eps' => 'nullable|numeric|min:0',
            'min_samples' => 'nullable|integer|min:1',
        ]);

        $eps = $request->input('eps', 0.7);
        $minSamples = $request->input('min_samples', 10);

        try {

            $query = Umkm::with('subdistrict');

            // Tambahkan filter wilayah ini
            if ($request->filled('kategori_wilayah')) {
                $query->whereHas('subdistrict', function ($q) use ($request) {
                    $q->where('kategori_wilayah', $request->kategori_wilayah);
                });
            }

            $umkms = $query->get();

            if ($umkms->isEmpty()) {
                return back()->with('error', 'Data UMKM kosong.');
            }

            $payloadData = $umkms->map(function ($item) {
                return [
                    'id' => $item->id,
                    'latitude' => (float) $item->latitude,
                    'longitude' => (float) $item->longitude,
                    'kecamatan' => optional($item->subdistrict)->name,
                    'kategori_kuliner' => $item->kategori,
                ];
            })->values();

            /** menentukan filter */
            $wilayah = $request->input('kategori_wilayah') ?: 'all';
            $kecamatan = $request->input('kecamatan') ?: 'all';
            $kategori = $request->input('kategori') ?: 'all';

            $filterKey = "wil_{$wilayah}_kec_{$kecamatan}_kat_{$kategori}";

            /** request ke Flask API */
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(120)->post(
                config('services.flask.url').'/cluster/api',
                [
                    'data' => $payloadData,
                    'eps' => $eps,
                    'min_samples' => $minSamples,
                    'kecamatan' => $kecamatan == 'all' ? null : $kecamatan,
                    'kategori_kuliner' => $kategori == 'all' ? null : $kategori,
                ]
            );

            if (! $response->successful()) {
                return back()->with(
                    'error',
                    'Gagal menghubungi Flask API. Status: '.$response->status()
                );
            }

            $result = $response->json();

            if (($result['status'] ?? null) !== 'success') {
                return back()->with('error', $result['message'] ?? 'Clustering gagal.');
            }

            $data = $result['data'];

            DB::beginTransaction();

            foreach ($data['data'] as $row) {

                $cluster = $row['cluster'];

                ClusterResult::updateOrCreate(
                    [
                        'umkm_id' => $row['id'],
                        'filter' => $filterKey,
                    ],
                    [
                        'cluster' => $cluster == -1 ? null : $cluster,
                        'is_noise' => $cluster == -1,
                        'eps' => $eps,
                        'min_samples' => $minSamples,
                        // Menangkap metric DBI dari Flask API yang baru
                        'davies_bouldin_index' => $data['davies_bouldin_index'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.umkm.index')
                ->with(
                    'success',
                    'Clustering berhasil dijalankan. '.
                        'Cluster: '.($data['jumlah_cluster'] ?? 0).
                        ' | Noise: '.($data['jumlah_noise'] ?? 0)
                );
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Terjadi kesalahan saat proses clustering: '.$e->getMessage()
            );
        }
    }

    public function gridSearch(Request $request)
    {
        $query = Umkm::with('subdistrict');

        // Tambahkan filter wilayah ini
        if ($request->filled('kategori_wilayah')) {
            $query->whereHas('subdistrict', function ($q) use ($request) {
                $q->where('kategori_wilayah', $request->kategori_wilayah);
            });
        }

        $umkms = $query->get();

        if ($umkms->isEmpty()) {
            return back()->with('error', 'Data UMKM kosong.');
        }

        $payloadData = $umkms->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->nama_usaha,
                'latitude' => (float) $item->latitude,
                'longitude' => (float) $item->longitude,
                'kecamatan' => optional($item->subdistrict)->name,
                'kategori_kuliner' => $item->kategori,
            ];
        })->values();

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout(180)->post(
            config('services.flask.url').'/cluster/grid-search/api',
            [
                'data' => $payloadData,
                'kecamatan' => $request->kecamatan,
                'kategori_kuliner' => $request->kategori_kuliner,
                'eps_start' => $request->eps_start ?? 0.2,
                'eps_end' => $request->eps_end ?? 1.0,
                'eps_step' => $request->eps_step ?? 0.1,
                'minpts_start' => $request->minpts_start ?? 4,
                'minpts_end' => $request->minpts_end ?? 10,
            ]
        );

        if (! $response->successful()) {
            return back()->with('error', 'API clustering gagal diakses.');
        }

        $result = $response->json();

        if (($result['status'] ?? null) !== 'success') {
            return back()->with('error', $result['message'] ?? 'Grid search gagal.');
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('response', $result['data']);
    }

    public function kDistance(Request $request)
    {
        $query = Umkm::with('subdistrict');

        // Filter Kategori Wilayah di level Laravel (Database)
        if ($request->filled('kategori_wilayah')) {
            $query->whereHas('subdistrict', function ($q) use ($request) {
                $q->where('kategori_wilayah', $request->kategori_wilayah);
            });
        }

        $umkms = $query->get();

        if ($umkms->isEmpty()) {
            return back()->with('error', 'Data UMKM kosong untuk filter tersebut.');
        }

        // Mapping payload dengan standar yang sama seperti fungsi lain
        $payloadData = $umkms->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->nama_usaha,
                'latitude' => (float) $item->latitude,
                'longitude' => (float) $item->longitude,
                'kecamatan' => optional($item->subdistrict)->name,
                'kategori_kuliner' => $item->kategori,
            ];
        })->values();

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout(180)->post(
            config('services.flask.url').'/cluster/k-distance/api',
            [
                'data' => $payloadData,
                'k_start' => (int) ($request->k_start ?? 3),
                'k_end' => (int) ($request->k_end ?? 6),
                // Hanya mengirim kecamatan dan kategori untuk informasi filter di Flask
                'kecamatan' => $request->kecamatan,
                'kategori_kuliner' => $request->kategori,
            ]
        );

        if (! $response->successful()) {
            return back()->with('error', 'API K-Distance gagal diakses. Status: '.$response->status());
        }

        $result = $response->json();

        if (($result['status'] ?? null) !== 'success') {
            return back()->with('error', $result['message'] ?? 'Analisis K-Distance gagal.');
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('k_distance_response', $result['data'])
            ->with('success', 'Analisis K-Distance Graph berhasil diselesaikan.');
    }

    public function clusterResults()
    {
        // 1. Data Keseluruhan (Semua Wilayah)
        $filterAll = 'wil_all_kec_all_kat_all';
        $summaryAll = ClusterResult::where('filter', $filterAll)->first();

        $clustersAll = ClusterResult::select('cluster', 'is_noise', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->where('filter', $filterAll)
            ->groupBy('cluster', 'is_noise')
            ->orderBy('is_noise')
            ->orderBy('cluster')
            ->get();

        // 2. Data Daratan Utama
        $filterDaratan = 'wil_daratan_utama_kec_all_kat_all';
        $summaryDaratan = ClusterResult::where('filter', $filterDaratan)->first();

        $clustersDaratan = ClusterResult::select('cluster', 'is_noise', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->where('filter', $filterDaratan)
            ->groupBy('cluster', 'is_noise')
            ->orderBy('is_noise') // Menempatkan cluster di atas, noise di bawah
            ->orderBy('cluster')
            ->get();

        // 3. Data Kepulauan
        $filterKepulauan = 'wil_kepulauan_kec_all_kat_all';
        $summaryKepulauan = ClusterResult::where('filter', $filterKepulauan)->first();

        $clustersKepulauan = ClusterResult::select('cluster', 'is_noise', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->where('filter', $filterKepulauan)
            ->groupBy('cluster', 'is_noise')
            ->orderBy('is_noise')
            ->orderBy('cluster')
            ->get();

        return view('admin.umkm.cluster-results', compact(
            'summaryAll', 'clustersAll',
            'summaryDaratan', 'clustersDaratan',
            'summaryKepulauan', 'clustersKepulauan'
        ));
    }

    public function exportCsv(Request $request)
    {
        $query = Umkm::query();

        // 1. Filter Kategori Wilayah (Daratan Utama / Kepulauan)
        if ($request->filled('kategori_wilayah') && $request->kategori_wilayah !== 'all') {
            $query->whereHas('subdistrict', function ($q) use ($request) {
                $q->where('kategori_wilayah', $request->kategori_wilayah);
            });
        }

        // 2. Filter Kecamatan (Berdasarkan subdistrict_id)
        if ($request->filled('kecamatan') && $request->kecamatan !== 'all') {
            $query->where('subdistrict_id', $request->kecamatan);
        }

        // 3. Filter Kategori Kuliner
        if ($request->filled('kategori') && $request->kategori !== 'all') {
            $query->where('kategori', $request->kategori);
        }

        // Ambil data berdasarkan filter
        $umkms = $query->get();

        // Penamaan file dinamis berdasarkan waktu ekspor
        $filename = 'data_umkm_'.date('Ymd_His').'.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // Header kolom sesuai permintaan (menggunakan separator ;)
        $columns = ['nama_usaha', 'kategori', 'alamat', 'subdistrict_id', 'latitude', 'longitude'];

        $callback = function () use ($umkms, $columns) {
            $file = fopen('php://output', 'w');

            // Tambahkan BOM untuk kompatibilitas UTF-8 di Microsoft Excel
            fwrite($file, $bom = (chr(0xEF).chr(0xBB).chr(0xBF)));

            // Tulis header
            fputcsv($file, $columns, ';');

            // Looping data UMKM
            foreach ($umkms as $umkm) {
                $row = [
                    $umkm->nama_usaha,
                    $umkm->kategori,
                    $umkm->alamat,
                    $umkm->subdistrict_id,
                    $umkm->latitude,
                    $umkm->longitude,
                ];

                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
