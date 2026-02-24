<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $fillable = [
        'nama_usaha',
        'kategori',
        'alamat',
        'subdistrict_id',
        'jam_operasional',
        'no_kontak',
        'rating',
        'jumlah_ulasan',
        'latitude',
        'longitude',
        'cluster_id',
        'is_noise',
    ];

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class, 'subdistrict_id', 'id');
    }
}

