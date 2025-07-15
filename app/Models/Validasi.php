<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Validasi extends Model
{
    protected $table = 'pengajuan_u_k_t_s';

    protected $fillable = [
        'nim',
        'nama',
        'juara',
        'nama_lomba',
        'link_kompetisi',
        'tingkat_kompetisi',
        'jumlah_peserta',
        'tanggal_pelaksanaan',
        'tempat_pelaksanaan',
        'lembaga_penyelenggara',
        'nama_dosen',
        'nip_dosen',
        'status',
        'komentar',
        'no_surat',
        'surat_tugas',
        'sertifikat_lomba',
        'file_peserta',
        'foto_kegiatan',
    ];

    protected $casts = [
        'foto_kegiatan' => 'array',
    ];


/**
     * Relasi ke model User (berdasarkan kolom nim)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nim', 'nim');
        // Format: belongsTo(TargetModel::class, foreign_key, owner_key)
    }

    /**
     * Relasi ke model RiwayatPengajuan (jika diperlukan)
     * (karena satu tabel, ini optional tergantung kebutuhan)
     */
    public function riwayat()
    {
        return $this->hasOne(RiwayatPengajuan::class, 'nim', 'nim');
    }
}