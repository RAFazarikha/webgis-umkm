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
        $kecamatan = $request->input('kecamatan') ?: 'all';
        $kategori = $request->input('kategori') ?: 'all';
        // $kecamatan = 'all';
        // $kategori = 'all';
        $search = $request->input('search');

        $filterKey = "kec_{$kecamatan}_kat_{$kategori}";

        // cek apakah ada hasil cluster
        $clusterExists = ClusterResult::where('filter', $filterKey)->exists();

        if ($clusterExists) {

            $umkms = Umkm::with([
                'subdistrict',
                'clusterResultAll' => function ($q) use ($filterKey) {
                    $q->where('filter', $filterKey);
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
            ->when($clusterExists, function ($q) use ($filterKey) {
                $q->whereHas('clusterResultAll', function ($q2) use ($filterKey) {
                    $q2->where('filter', $filterKey);
                });
            })
            ->get();

        } else {
            $filterKey = "kec_all_kat_all";

            $umkms = Umkm::with([
                'subdistrict',
                'clusterResultAll' => function ($q) use ($filterKey) {
                    $q->where('filter', $filterKey);
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
            ->when($clusterExists, function ($q) use ($filterKey) {
                $q->whereHas('clusterResultAll', function ($q2) use ($filterKey) {
                    $q2->where('filter', $filterKey);
                });
            })
            ->get();

        }

        // =========================
        // SEARCH UMKM
        // =========================

        $selectedUmkm = null;

        if ($search) {

            $selectedUmkm = Umkm::with('subdistrict')
                ->where('nama_usaha','like',"%{$search}%")
                ->first();

        }

        $kecamatans = Subdistrict::all();

        return view('map', compact(
            'umkms',
            'kecamatans',
            'kecamatan',
            'kategori',
            'clusterExists',
            'selectedUmkm'
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

    public function view(Request $request, $id)
    {
        $kecamatan = $request->input('kecamatan') ?: 'all';
        $kategori = $request->input('kategori') ?: 'all';

        $filterKey = "kec_{$kecamatan}_kat_{$kategori}";

        $umkm = Umkm::with([
            'subdistrict',
            'clusterResultAll' => function ($q) use ($filterKey) {
                $q->where('filter', $filterKey);
            }
        ])->findOrFail($id);

        return view('kuliner.view', compact('umkm'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $umkms = Umkm::where('nama_usaha', 'like', "%{$query}%")
            ->orWhere('kategori', 'like', "%{$query}%")
            ->orWhere('alamat', 'like', "%{$query}%")
            ->with('subdistrict')
            ->latest()
            ->paginate(10);

        return view('kuliner', compact('umkms'));
    }
}
