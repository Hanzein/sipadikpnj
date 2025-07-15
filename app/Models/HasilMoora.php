<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HasilMoora extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_u_k_t_s';

    protected $fillable = [
        'nama',
        'nim',
        'no_surat',
        'juara',
        'nama_lomba',
        'tingkat_kompetisi',
        'score_moora',
        'nominal_apresiasi',
        'peringkat',
    ];
}
