<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa'; 

    public function profile(): HasOne
    {
        return $this->hasOne(ProfileMahasiswa::class);
    }

    public function prestasi(): HasMany
    {
        return $this->hasMany(Prestasi::class);
    }
}
