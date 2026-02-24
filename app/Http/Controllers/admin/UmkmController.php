<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subdistrict;
use App\Models\Umkm;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function index()
    {
        $umkms = Umkm::with('subdistrict')->latest()->paginate(10);
        return view('admin.umkm.index', compact('umkms'));
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
        ]);

        Umkm::create($validated + $request->only([
            'jam_operasional',
            'no_kontak',
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
            'no_kontak',
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
}
