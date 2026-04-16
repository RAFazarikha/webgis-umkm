<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use Sluggable;

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
        'slug'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nama_usaha'
            ]
        ];
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class, 'subdistrict_id', 'id');
    }

    public function clusterResultAll()
    {
        return $this->hasMany(ClusterResult::class,'umkm_id');
    }
}

