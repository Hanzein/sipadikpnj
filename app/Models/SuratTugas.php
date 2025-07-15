<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    protected $table = 'db_surat_tugas';

    protected $fillable = [
        'no_surat',
        'tanggal',
        'surat_tugas',
    ];
}
