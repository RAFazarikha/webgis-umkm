<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $guarded = ['id'];

    // Relasi: Satu UMKM memiliki banyak foto
    public function photos()
    {
        return $this->hasMany(UmkmPhoto::class);
    }

    // Helper untuk mengambil foto utama (untuk popup Leaflet.js)
    public function primaryPhoto()
    {
        return $this->hasOne(UmkmPhoto::class)->where('is_primary', true)->latest();
    }
}
