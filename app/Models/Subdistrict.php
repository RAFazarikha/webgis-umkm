<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
    protected $fillable = ['name'];

    public function umkms()
    {
        return $this->hasMany(Umkm::class);
    }
}
