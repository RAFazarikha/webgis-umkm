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
        $topumkm = Umkm::orderBy('rating', 'desc')->take(6)->get();

        return view('home', compact('topumkm'));
    }

    public function map(Request $request)
    {
        $kecamatan = $request->input('kecamatan') ?: 'all';
        $kategori = $request->input('kategori') ?: 'all';
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
                ->whereHas('clusterResultAll', function ($q) use ($filterKey) {
                    $q->where('filter', $filterKey);
                })
                ->get();

        } else {

            $umkms = Umkm::with('subdistrict')->get();

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

    public function kuliner()
    {
        $umkms = Umkm::with('subdistrict')->latest()->paginate(10);

        return view('kuliner', compact('umkms'));
    }

    public function tentang()
    {
        return view('tentang');
    }

    public function view($id)
    {
        $umkm = Umkm::with('subdistrict')->findOrFail($id);
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
