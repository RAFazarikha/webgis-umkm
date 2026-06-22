<?php

namespace App\Http\Controllers;

use App\Models\ClusterResult;
use App\Models\Subdistrict;
use App\Models\Umkm;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home()
    {
        $topumkm = Umkm::orderBy('jumlah_ulasan', 'desc')
            ->orderBy('rating', 'desc')
            ->take(12)->get();

        return view('home', compact('topumkm'));
    }

    public function map(Request $request)
    {
        // Menggunakan parameter default 'all'
        $wilayah = $request->input('wilayah', 'all');
        $kecamatan = $request->input('kecamatan', 'all');
        $kategori = $request->input('kategori', 'all');
        $cluster = $request->input('cluster', 'all');
        $search = $request->input('search');

        // 1. Logika Kunci Filter (Single String)
        $filterKey = "wil_{$wilayah}_kec_{$kecamatan}_kat_{$kategori}";
        $defaultFilter = 'wil_all_kec_all_kat_all';

        // Cek apakah ada hasil cluster untuk filter yang dipilih
        $clusterExists = ClusterResult::where('filter', $filterKey)->exists();

        // Tentukan filter key mana yang aktif diload
        $activeFilter = $clusterExists ? $filterKey : $defaultFilter;

        // 2. Query Utama
        $umkms = Umkm::with([
            'subdistrict',
            'clusterResultAll' => function ($q) use ($activeFilter) {
                $q->where('filter', $activeFilter);
            },
        ])
            // Filter Kategori Wilayah (Daratan Utama / Kepulauan)
            ->when($wilayah !== 'all', function ($q) use ($wilayah) {
                $q->whereHas('subdistrict', function ($sub) use ($wilayah) {
                    $sub->where('kategori_wilayah', $wilayah);
                });
            })
            // Filter Kecamatan
            ->when($kecamatan !== 'all', function ($q) use ($kecamatan) {
                $q->whereHas('subdistrict', function ($sub) use ($kecamatan) {
                    $sub->where('name', $kecamatan);
                });
            })
            // Filter Kategori Kuliner
            ->when($kategori !== 'all', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            })
            // Filter Cluster / Noise
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
            // Menampilkan hanya yang punya hasil cluster pada filter aktif
            ->when($clusterExists, function ($q) use ($activeFilter) {
                $q->whereHas('clusterResultAll', function ($q2) use ($activeFilter) {
                    $q2->where('filter', $activeFilter);
                });
            })
            // Filter pencarian nama usaha
            ->when($search, function ($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%");
            })
            ->get();

        // Mengambil daftar list cluster untuk menu Dropdown
        $clusters = ClusterResult::where('filter', $defaultFilter)
            ->whereNotNull('cluster')
            ->distinct()
            ->orderBy('cluster', 'asc')
            ->pluck('cluster');

        $kecamatans = Subdistrict::all();

        // ----------------------------------------------------
        // MENGAMBIL PARAMETER, DBI, & JUMLAH CLUSTER UNTUK PETA
        // ----------------------------------------------------

        // 1. Keseluruhan (All)
        $filterAll = "wil_all_kec_{$kecamatan}_kat_{$kategori}";
        $epsAll = ClusterResult::where('filter', $filterAll)->value('eps') ?? '-';
        $minptsAll = ClusterResult::where('filter', $filterAll)->value('min_samples') ?? '-';
        $dbiAll = ClusterResult::where('filter', $filterAll)->value('davies_bouldin_index') ?? '-';
        $jmlClusterAll = ClusterResult::where('filter', $filterAll)->whereNotNull('cluster')->distinct('cluster')->count('cluster');

        // 2. Daratan Utama
        $filterDaratan = "wil_daratan_utama_kec_{$kecamatan}_kat_{$kategori}";
        $epsDaratan = ClusterResult::where('filter', $filterDaratan)->value('eps') ?? '-';
        $minptsDaratan = ClusterResult::where('filter', $filterDaratan)->value('min_samples') ?? '-';
        $dbiDaratan = ClusterResult::where('filter', $filterDaratan)->value('davies_bouldin_index') ?? '-';
        $jmlClusterDaratan = ClusterResult::where('filter', $filterDaratan)->whereNotNull('cluster')->distinct('cluster')->count('cluster');

        // 3. Kepulauan
        $filterKepulauan = "wil_kepulauan_kec_{$kecamatan}_kat_{$kategori}";
        $epsKepulauan = ClusterResult::where('filter', $filterKepulauan)->value('eps') ?? '-';
        $minptsKepulauan = ClusterResult::where('filter', $filterKepulauan)->value('min_samples') ?? '-';
        $dbiKepulauan = ClusterResult::where('filter', $filterKepulauan)->value('davies_bouldin_index') ?? '-';
        $jmlClusterKepulauan = ClusterResult::where('filter', $filterKepulauan)->whereNotNull('cluster')->distinct('cluster')->count('cluster');

        return view('map', compact(
            'umkms',
            'kecamatans',
            'wilayah',
            'kecamatan',
            'kategori',
            'clusterExists',
            'clusters',
            'epsAll',
            'minptsAll',
            'dbiAll',
            'jmlClusterAll',
            'epsDaratan',
            'minptsDaratan',
            'dbiDaratan',
            'jmlClusterDaratan',
            'epsKepulauan',
            'minptsKepulauan',
            'dbiKepulauan',
            'jmlClusterKepulauan'
        ));
    }

    public function kuliner(Request $request)
    {
        $search = $request->input('search');

        $umkms = Umkm::with('subdistrict')
            ->when($search, function ($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('kuliner', compact('umkms'));
    }

    public function tentang()
    {
        return view('tentang');
    }

    public function view(Request $request, $slug)
    {
        $wilayah = $request->input('wilayah') ?: 'all';
        $kecamatan = $request->input('kecamatan') ?: 'all';
        $kategori = $request->input('kategori') ?: 'all';

        $filterKey = "wil_{$wilayah}_kec_{$kecamatan}_kat_{$kategori}";

        $umkm = Umkm::with([
            'subdistrict',
            'clusterResultAll' => function ($q) use ($filterKey) {
                $q->where('filter', $filterKey);
            },
        ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('kuliner.view', compact('umkm'));
    }

    public function cari(Request $request)
    {
        $query = $request->input('search');

        $umkms = Umkm::where('nama_usaha', 'like', "%{$query}%")
            ->orWhere('kategori', 'like', "%{$query}%")
            ->orWhere('alamat', 'like', "%{$query}%")
            ->with('subdistrict')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kecamatans = Subdistrict::all();

        return view('cari', compact('umkms', 'kecamatans'));
    }

    public function cluster(Request $request)
    {
        $query = $request->input('cluster');

        $umkms = Umkm::whereHas('clusterResultAll', function ($q) use ($query) {
            $q->where('cluster', $query);
        })
            ->with('subdistrict')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $kecamatans = Subdistrict::all();

        $umkmsForMap = Umkm::whereHas('clusterResultAll', function ($q) use ($query) {
            $q->where('cluster', $query);
        })
            ->with('subdistrict')
            ->latest()
            ->get();

        $clusters = ClusterResult::where('filter', 'wil_all_kec_all_kat_all')
            ->whereNotNull('cluster')
            ->distinct()
            ->orderBy('cluster', 'asc')
            ->pluck('cluster');

        return view('cluster', compact('umkms', 'kecamatans', 'clusters', 'umkmsForMap'));
    }
}
