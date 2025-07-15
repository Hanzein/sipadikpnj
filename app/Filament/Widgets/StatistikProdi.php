<?php

namespace App\Filament\Widgets;

use App\Models\RiwayatPengajuan;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class StatistikProdi extends Widget
{
    protected static string $view = 'filament.widgets.statistik-prodi';
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        /* ----------------------------------------------------------
         |  JOIN memakai kolom NIM, karena itulah FK-nya.
         |  Jika Anda memakai nama kolom lain, ganti 'nim' dengan kolom itu.
         |---------------------------------------------------------- */
        $statistik = RiwayatPengajuan::query()
            ->select('users.prodi', DB::raw('count(*) as total'))
            ->join('users', 'users.nim', '=', 'pengajuan_u_k_t_s.nim')   // ⬅️ diubah
            ->where('pengajuan_u_k_t_s.status', 'accepted')            // ganti 'disetujui' ➜ 'accepted' jika perlu
            ->groupBy('users.prodi')
            ->orderByDesc('total')
            ->get();

        return compact('statistik');
    }
}
