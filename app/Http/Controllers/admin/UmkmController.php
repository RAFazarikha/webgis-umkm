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
    public function index()
    {
        $umkms = Umkm::with('subdistrict')->with('clusterResultAll')->latest()->paginate(10);
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
        ]);

        Umkm::create($validated + $request->only([
            'jam_operasional',
            'rating',
            'jumlah_ulasan'
        ]));

        return redirect()->route('admin.umkm.index')
            ->with('success', 'UMKM berhasil ditambahkan');
    }

    public function edit(Umkm $umkm)
    {
        $kecamatan = Subdistrict::all();

        return view('admin.umkm.edit', compact('umkm', 'kecamatan'));
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
        ]);

        $umkm->update($validated + $request->only([
            'jam_operasional',
            'rating',
            'jumlah_ulasan'
        ]));

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

            // Validasi header
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
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            fclose($handle);

            // Insert batch
            Umkm::insert($data);

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
