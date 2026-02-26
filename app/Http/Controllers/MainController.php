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
        return view('map');
    }

    public function kuliner()
    {
        return view('kuliner');
    }

    public function tentang()
    {
        return view('tentang');
    }
}
