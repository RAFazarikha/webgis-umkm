<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subdistrict extends Model
{
    protected $table = 'subdistricts';

    protected $fillable = ['name'];

    public function umkms()
    {
        return $this->hasMany(Umkm::class);
    }
}
