<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_u_k_t_s';

    protected $fillable = [
        'nim',
        'surat_tugas',
        'no_surat',
        'sertifikat_lomba',
        'nama_lomba',
        'nama',
        'juara',
        'tingkat_kompetisi',
        'jumlah_peserta',
        'tanggal_pelaksanaan',
        'periode_semester',
        'tempat_pelaksanaan',
        'lembaga_penyelenggara',
        'link_kompetisi',
        'foto_web_kompetisi',
        'nama_dosen',
        'nip_dosen',
        'file_peserta',
        'foto_kegiatan',
    ];

    protected $casts = [
        'foto_kegiatan' => 'array',
    ];

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'nim', 'nim');
    }

    protected static function booted()
{
    static::saved(function () {
        app(\App\Services\MooraService::class)->calculateMoora();
    });

    static::deleted(function () {
        app(\App\Services\MooraService::class)->calculateMoora();
    });
}

}
