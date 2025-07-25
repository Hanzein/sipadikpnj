<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;

    protected $fillable = ['mahasiswa_id', 'tingkat_kejuaraan', 'peringkat'];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
