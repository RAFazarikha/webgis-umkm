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
        'rating',
        'jumlah_ulasan',
        'latitude',
        'longitude',
    ];

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class, 'subdistrict_id', 'id');
    }

    public function clusterResultNone()
    {
        return $this->hasOne(ClusterResult::class, 'umkm_id', 'id')->where('filter', 'none');
    }
}

