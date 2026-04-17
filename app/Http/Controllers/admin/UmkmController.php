<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClusterResult;
use App\Models\Subdistrict;
use App\Models\Umkm;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UmkmController extends Controller
{
    public function index()
    {
        $filterKey = 'kec_all_kat_all';

        $clusterExists = ClusterResult::where('filter', $filterKey)->exists();

        if ($clusterExists) {

            $umkms = Umkm::with([
                    'subdistrict',
                    'clusterResultAll' => function ($query) use ($filterKey) {
                        $query->where('filter', $filterKey);
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {
            $umkms = Umkm::with('subdistrict')->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(10);
        }

        return view('admin.umkm.index', compact('umkms'));
    }

    public function dashboard()
    {
        $totalUmkm = Umkm::count();
        $totalCluster = ClusterResult::whereNotNull('cluster')->where('filter', 'kec_all_kat_all')->distinct('cluster')->count('cluster');
        $totalNoise = ClusterResult::where('is_noise', true)->where('filter', 'kec_all_kat_all')->count();
        $avgRating = round(Umkm::avg('rating'), 1);

        $kecamatans = Subdistrict::withCount('umkms')->get();

        return view('admin.dashboard', compact(
            'totalUmkm',
            'totalCluster',
            'totalNoise',
            'avgRating',
            'kecamatans'
        ));
    }

    public function create()
    {
        $kecamatan = Subdistrict::all();

        return view('admin.umkm.create', compact('kecamatan'));
    }

    // public function show($id)
    // {
    //     $umkm = Umkm::findOrFail($id);

    //     return view('admin.umkm.show', compact('umkm'));
    // }

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
            'jumlah_ulasan' => 'nullable|integer|min:0'
        ]);

        // Menggabungkan "08:00" dan "16:00" menjadi "08:00 - 16:00"
        $jam_operasional = $request->jam_buka . ' - ' . $request->jam_tutup;

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
            'jumlah_ulasan' => $validated['jumlah_ulasan'] ?? null
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
                $jam_buka  = str_replace('.', ':', $pecah_jam[0]);
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
            'jumlah_ulasan' => 'nullable|integer|min:0'
        ]);

        if ($request->jam_buka && $request->jam_tutup) {
            $validated['jam_operasional'] = str_replace(':', '.', $request->jam_buka) . ' - ' . str_replace(':', '.', $request->jam_tutup);
        }

        $umkm->update([
            'nama_usaha' => $validated['nama_usaha'],
            'kategori' => $validated['kategori'],
            'alamat' => $validated['alamat'],
            'subdistrict_id' => $validated['subdistrict_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'jam_operasional' => ($request->jam_buka && $request->jam_tutup) ? str_replace(':', '.', $request->jam_buka) . ' - ' . str_replace(':', '.', $request->jam_tutup) : null,
            'rating' => $validated['rating'] ?? null,
            'jumlah_ulasan' => $validated['jumlah_ulasan'] ?? null
        ]);

        return redirect()->route('admin.umkm.index')
            ->with('success', 'UMKM berhasil diperbarui');
    }

    public function destroy(Umkm $umkm)
    {
        $umkm->delete();
        return back()->with('success', 'UMKM berhasil dihapus');
    }

    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:csv,txt|max:2048'
    //     ]);

    //     $file = $request->file('file');

    //     if (($handle = fopen($file->getRealPath(), 'r')) !== false) {

    //         $header = fgetcsv($handle, 1000, ';');

    //         $expectedHeader = [
    //             'nama_usaha',
    //             'kategori',
    //             'alamat',
    //             'subdistrict_id',
    //             'jam_buka',
    //             'jam_tutup',
    //             'rating',
    //             'jumlah_ulasan',
    //             'latitude',
    //             'longitude',
    //             'cluster_id',
    //             'is_noise'
    //         ];

    //         // Validasi header
    //         if ($header !== $expectedHeader) {
    //             return back()->with('error', 'Format header CSV tidak sesuai.');
    //         }

    //         $data = [];
    //         $slugsPelacak = [];

    //         while (($row = fgetcsv($handle, 1000, ';')) !== false) {

    //             $baseSlug = SlugService::createSlug(Umkm::class, 'slug', $row[0]);
    //             $slugUnik = $baseSlug;
    //             $counter = 1;

    //             while (in_array($slugUnik, $slugsPelacak)) {
    //                 $slugUnik = $baseSlug . '-' . $counter;
    //                 $counter++;
    //             }

    //             $slugsPelacak[] = $slugUnik;

    //             $data[] = [
    //                 'nama_usaha'      => $row[0],
    //                 'kategori'        => $row[1],
    //                 'alamat'          => $row[2],
    //                 'subdistrict_id'  => (int) $row[3],
    //                 'jam_operasional' => ($row[4] && $row[5]) ? $row[4] . ' - ' . $row[5] : null,
    //                 'rating'          => $row[6] !== '' ? (float) $row[6] : null,
    //                 'jumlah_ulasan'   => $row[7] !== '' ? (int) $row[7] : null,
    //                 'latitude'        => (float) $row[8],
    //                 'longitude'       => (float) $row[9],
    //                 'slug'            => $slugUnik,
    //                 'created_at'      => now(),
    //                 'updated_at'      => now(),
    //             ];
    //         }

    //         fclose($handle);

    //         // Insert batch
    //         Umkm::insert($data);

    //         return redirect()->route('admin.umkm.index')
    //             ->with('success', 'Data CSV berhasil diimport.');
    //     }

    //     return back()->with('error', 'File tidak dapat dibaca.');
    // }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
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
                'is_noise'
            ];

            if ($header !== $expectedHeader) {
                return back()->with('error', 'Format header CSV tidak sesuai.');
            }

            $data = [];

            while (($row = fgetcsv($handle, 1000, ';')) !== false) {
                $data[] = [
                    'nama_usaha'      => $row[0],
                    'kategori'        => $row[1],
                    'alamat'          => $row[2],
                    'subdistrict_id'  => (int) $row[3],
                    'jam_operasional' => ($row[4] && $row[5]) ? $row[4] . ' - ' . $row[5] : null,
                    'rating'          => $row[6] !== '' ? (float) $row[6] : null,
                    'jumlah_ulasan'   => $row[7] !== '' ? (int) $row[7] : null,
                    'latitude'        => (float) $row[8],
                    'longitude'       => (float) $row[9],
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
            'min_samples' => 'nullable|integer|min:1'
        ]);

        $eps = $request->input('eps', 0.7);
        $minSamples = $request->input('min_samples', 10);

        try {

            $umkms = Umkm::with('subdistrict')->get();

            if ($umkms->isEmpty()) {
                return back()->with('error', 'Data UMKM kosong.');
            }

            $payloadData = $umkms->map(function ($item) {
                return [
                    'id' => $item->id,
                    'latitude' => (float) $item->latitude,
                    'longitude' => (float) $item->longitude,
                    'kecamatan' => optional($item->subdistrict)->name,
                    'kategori_kuliner' => $item->kategori
                ];
            })->values();

            /** menentukan filter */
            $kecamatan = $request->input('kecamatan') ?: 'all';
            $kategori = $request->input('kategori') ?: 'all';

            $filterKey = "kec_{$kecamatan}_kat_{$kategori}";

            /** request ke Flask API */
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(120)->post(
                config('services.flask.url') . '/cluster/api',
                [
                    'data' => $payloadData,
                    'eps' => $eps,
                    'min_samples' => $minSamples,
                    'kecamatan' => $kecamatan == 'all' ? null : $kecamatan,
                    'kategori_kuliner' => $kategori == 'all' ? null : $kategori
                ]
            );

            if (!$response->successful()) {
                return back()->with(
                    'error',
                    'Gagal menghubungi Flask API. Status: ' . $response->status()
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
                        'filter' => $filterKey
                    ],
                    [
                        'cluster' => $cluster == -1 ? null : $cluster,
                        'is_noise' => $cluster == -1,
                        'eps' => $eps,
                        'min_samples' => $minSamples,
                        'silhouette_score' => $data['silhouette_coefficient'] ?? null
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.umkm.index')
                ->with(
                    'success',
                    'Clustering berhasil dijalankan. ' .
                    'Cluster: ' . ($data['jumlah_cluster'] ?? 0) .
                    ' | Noise: ' . ($data['jumlah_noise'] ?? 0)
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Terjadi kesalahan saat proses clustering: ' . $e->getMessage()
            );
        }
    }

    public function gridSearch(Request $request)
    {
        $umkms = Umkm::with('subdistrict')->get();

        if ($umkms->isEmpty()) {
            return back()->with('error', 'Data UMKM kosong.');
        }

        $payloadData = $umkms->map(function ($item) {
            return [
                "id" => $item->id,
                "name" => $item->nama_usaha,
                "latitude" => (float) $item->latitude,
                "longitude" => (float) $item->longitude,
                "kecamatan" => optional($item->subdistrict)->name,
                "kategori_kuliner" => $item->kategori
            ];
        })->values();

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::timeout(180)->post(
            config('services.flask.url') . '/cluster/grid-search/api',
            [
                "data" => $payloadData,
                "kecamatan" => $request->kecamatan,
                "kategori_kuliner" => $request->kategori_kuliner,
                "eps_start" => $request->eps_start ?? 0.2,
                "eps_end" => $request->eps_end ?? 1.0,
                "eps_step" => $request->eps_step ?? 0.1,
                "minpts_start" => $request->minpts_start ?? 4,
                "minpts_end" => $request->minpts_end ?? 10
            ]
        );

        if (!$response->successful()) {
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
}
