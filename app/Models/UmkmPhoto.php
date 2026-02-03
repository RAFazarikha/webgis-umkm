<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UmkmPhoto extends Model
{
    protected $guarded = ['id'];

    // Relasi: Foto milik satu UMKM
    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}
