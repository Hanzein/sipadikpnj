<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class profile_mahasiswa extends Model
{
    protected $table = 'profile_mahasiswa'; 
    
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
