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
        // Menggunakan parameter default dari Laravel
        $wilayah = $request->input('wilayah', 'all');
        $kecamatan = $request->input('kecamatan', 'all');
        $kategori = $request->input('kategori', 'all');
        $cluster = $request->input('cluster', 'all');
        $search = $request->input('search');

        // 1. Logika Kunci Filter (Array)
        // Jika 'all', kita cari data untuk daratan_utama DAN kepulauan sekaligus
        if ($wilayah === 'all') {
            $filterKeys = [
                "wil_daratan_utama_kec_{$kecamatan}_kat_{$kategori}",
                "wil_kepulauan_kec_{$kecamatan}_kat_{$kategori}"
            ];
            $defaultFilters = [
                "wil_daratan_utama_kec_all_kat_all",
                "wil_kepulauan_kec_all_kat_all"
            ];
        } else {
            $filterKeys = ["wil_{$wilayah}_kec_{$kecamatan}_kat_{$kategori}"];
            $defaultFilters = ["wil_{$wilayah}_kec_all_kat_all"];
        }

        // Cek apakah ada hasil cluster (menggunakan whereIn karena bisa berupa array)
        $clusterExists = ClusterResult::whereIn('filter', $filterKeys)->exists();

        // Tentukan filter key mana yang akan diload
        $activeFilters = $clusterExists ? $filterKeys : $defaultFilters;

        // Query Utama
        $umkms = Umkm::with([
            'subdistrict',
            'clusterResultAll' => function ($q) use ($activeFilters) {
                // Gunakan whereIn agar bisa membaca lebih dari satu filter sekaligus
                $q->whereIn('filter', $activeFilters);
            }
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
            ->when($cluster !== 'all', function ($q) use ($cluster, $defaultFilters) {
                $q->whereHas('clusterResultAll', function ($q2) use ($cluster, $defaultFilters) {
                    // Gunakan defaultFilters agar dropdown filter cluster berfungsi dengan baik
                    $q2->whereIn('filter', $defaultFilters);

                    if ($cluster === 'noise') {
                        $q2->where('is_noise', true);
                    } else {
                        // Cast ke (int) untuk memastikan tipe data cocok dengan database
                        $q2->where('cluster', (int) $cluster);
                    }
                });
            })
            // Menampilkan hanya yang punya hasil cluster pada filter aktif
            ->when($clusterExists, function ($q) use ($filterKeys) {
                $q->whereHas('clusterResultAll', function ($q2) use ($filterKeys) {
                    $q2->whereIn('filter', $filterKeys);
                });
            })
            // Tambahan opsional: filter search jika di peta ada kolom pencarian nama usaha
            ->when($search, function ($q) use ($search) {
                $q->where('nama_usaha', 'like', "%{$search}%");
            })
            ->get();

        // Mengambil daftar list cluster untuk menu Dropdown
        $clusters = ClusterResult::whereIn('filter', $defaultFilters)
            ->whereNotNull('cluster')
            ->distinct()
            ->orderBy('cluster', 'asc')
            ->pluck('cluster');

        $kecamatans = Subdistrict::all();

        // ----------------------------------------------------
        // MENGAMBIL PARAMETER & JUMLAH CLUSTER UNTUK DESKRIPSI PETA
        // ----------------------------------------------------

        // 1. Daratan Utama
        $filterDaratan = "wil_daratan_utama_kec_{$kecamatan}_kat_{$kategori}";
        $epsDaratan = ClusterResult::where('filter', $filterDaratan)->value('eps') ?? '-';
        $minptsDaratan = ClusterResult::where('filter', $filterDaratan)->value('min_samples') ?? '-';
        $jmlClusterDaratan = ClusterResult::where('filter', $filterDaratan)->whereNotNull('cluster')->distinct('cluster')->count('cluster');

        // 2. Kepulauan
        $filterKepulauan = "wil_kepulauan_kec_{$kecamatan}_kat_{$kategori}";
        $epsKepulauan = ClusterResult::where('filter', $filterKepulauan)->value('eps') ?? '-';
        $minptsKepulauan = ClusterResult::where('filter', $filterKepulauan)->value('min_samples') ?? '-';
        $jmlClusterKepulauan = ClusterResult::where('filter', $filterKepulauan)->whereNotNull('cluster')->distinct('cluster')->count('cluster');

        // Jangan lupa tambahkan variabel baru ini ke dalam compact()
        return view('map', compact(
            'umkms',
            'kecamatans',
            'wilayah',
            'kecamatan',
            'kategori',
            'clusterExists',
            'clusters',
            'epsDaratan',
            'minptsDaratan',
            'jmlClusterDaratan',
            'epsKepulauan',
            'minptsKepulauan',
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
        $kecamatan = $request->input('kecamatan') ?: 'all';
        $kategori = $request->input('kategori') ?: 'all';

        $filterKey = "kec_{$kecamatan}_kat_{$kategori}";

        $umkm = Umkm::with([
            'subdistrict',
            'clusterResultAll' => function ($q) use ($filterKey) {
                $q->where('filter', $filterKey);
            }
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

        $clusters = ClusterResult::where('filter', 'kec_all_kat_all')
            ->whereNotNull('cluster')
            ->distinct()
            ->orderBy('cluster', 'asc')
            ->pluck('cluster');

        return view('cluster', compact('umkms', 'kecamatans', 'clusters', 'umkmsForMap'));
    }
}
