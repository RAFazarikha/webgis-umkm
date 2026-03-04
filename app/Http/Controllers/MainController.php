<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home()
    {
        $topumkm = Umkm::orderBy('rating', 'desc')->take(6)->get();

        return view('home', compact('topumkm'));
    }

    public function map()
    {
        $umkms = Umkm::with('subdistrict')->get();

        return view('map', compact('umkms'));
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
}
