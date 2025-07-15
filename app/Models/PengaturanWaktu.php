<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PengaturanWaktu extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_waktu';

    protected $fillable = [
        'nama_periode',
        'tanggal_buka',
        'tanggal_tutup',
        'semester',
        'tahun_akademik',
        'is_active',
        'deskripsi',
        'batas_pengajuan'
    ];

    protected $casts = [
        'tanggal_buka' => 'datetime',
        'tanggal_tutup' => 'datetime',
        'is_active' => 'boolean',
        'batas_pengajuan' => 'integer'
    ];

    // Scope untuk mendapatkan periode aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk mendapatkan periode yang sedang berlangsung
    public function scopeCurrent($query)
    {
        $now = Carbon::now();
        return $query->where('tanggal_buka', '<=', $now)
                    ->where('tanggal_tutup', '>=', $now)
                    ->where('is_active', true);
    }

    // Method untuk mengecek apakah periode sedang aktif
    public function isCurrentlyActive(): bool
    {
        $now = Carbon::now();
        return $this->is_active && 
               $this->tanggal_buka <= $now && 
               $this->tanggal_tutup >= $now;
    }

    // Method untuk mengecek apakah periode akan segera dimulai
    public function isUpcoming(): bool
    {
        $now = Carbon::now();
        return $this->is_active && $this->tanggal_buka > $now;
    }

    // Method untuk mengecek apakah periode sudah berakhir
    public function isExpired(): bool
    {
        $now = Carbon::now();
        return $this->tanggal_tutup < $now;
    }

    // Method untuk mendapatkan status periode
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'nonaktif';
        }

        $now = Carbon::now();
        
        if ($this->tanggal_buka > $now) {
            return 'akan_dimulai';
        }
        
        if ($this->tanggal_tutup < $now) {
            return 'berakhir';
        }
        
        return 'aktif';
    }

    // Method untuk mendapatkan sisa waktu
    public function getRemainingTime(): ?string
    {
        if (!$this->isCurrentlyActive()) {
            return null;
        }

        $now = Carbon::now();
        $remaining = $this->tanggal_tutup->diff($now);

        if ($remaining->days > 0) {
            return $remaining->days . ' hari lagi';
        } elseif ($remaining->h > 0) {
            return $remaining->h . ' jam lagi';
        } else {
            return $remaining->i . ' menit lagi';
        }
    }

    // Method untuk mendapatkan periode aktif saat ini
    public static function getCurrentPeriod(): ?self
    {
        return self::current()->first();
    }

    // Method untuk menonaktifkan periode lain ketika periode ini diaktifkan
    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            if ($model->is_active) {
                // Nonaktifkan periode lain
                self::where('id', '!=', $model->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }
}