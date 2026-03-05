<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusterResult extends Model
{
    use HasFactory;

    protected $table = 'cluster_results';

    protected $fillable = [
        'umkm_id',
        'cluster',
        'is_noise',
        'filter',
    ];

    /**
     * Relasi ke UMKM
     */
    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}
