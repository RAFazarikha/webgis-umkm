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
        // Menggunakan parameter default dari Laravel, aman untuk nilai "0"
        $kecamatan = $request->input('kecamatan', 'all');
        $kategori = $request->input('kategori', 'all');
        $cluster = $request->input('cluster', 'all');

        $search = $request->input('search');

        $filterKey = "kec_{$kecamatan}_kat_{$kategori}";

        // cek apakah ada hasil cluster
        $clusterExists = ClusterResult::where('filter', $filterKey)->exists();

        // Tentukan filter key mana yang akan diload (yang spesifik atau yang all)
        $activeFilterKey = $clusterExists ? $filterKey : "kec_all_kat_all";

        // Query Utama (Digabung agar tidak mengulang if-else yang isinya sama)
        $umkms = Umkm::with([
                'subdistrict',
                'clusterResultAll' => function ($q) use ($activeFilterKey) {
                    $q->where('filter', $activeFilterKey);
                }
            ])
            ->when($kecamatan !== 'all', function ($q) use ($kecamatan) {
                $q->whereHas('subdistrict', function ($sub) use ($kecamatan) {
                    $sub->where('name', $kecamatan);
                });
            })
            ->when($kategori !== 'all', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            })
            ->when($cluster !== 'all', function ($q) use ($cluster) {
                $q->whereHas('clusterResultAll', function ($q2) use ($cluster) {
                    // Sesuai kode Anda, filter cluster mengambil dari kec_all_kat_all
                    $q2->where('filter', 'kec_all_kat_all');

                    if ($cluster === 'noise') {
                        $q2->where('is_noise', true);
                    } else {
                        // Cast ke (int) untuk memastikan tipe data cocok dengan database
                        $q2->where('cluster', (int) $cluster);
                    }
                });
            })
            ->when($clusterExists, function ($q) use ($filterKey) {
                $q->whereHas('clusterResultAll', function ($q2) use ($filterKey) {
                    $q2->where('filter', $filterKey);
                });
            })
            ->get();

        // Mengambil daftar list cluster
        $clusters = ClusterResult::where('filter', 'kec_all_kat_all')
            ->whereNotNull('cluster')
            ->distinct()
            ->orderBy('cluster', 'asc')
            ->pluck('cluster');

        $kecamatans = Subdistrict::all();

        $epsilon = ClusterResult::distinct()->pluck('eps')->first();
        $minpts = ClusterResult::distinct()->pluck('min_samples')->first();

        return view('map', compact(
            'umkms',
            'kecamatans',
            'kecamatan',
            'kategori',
            'clusterExists',
            'clusters',
            'epsilon',
            'minpts'
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
