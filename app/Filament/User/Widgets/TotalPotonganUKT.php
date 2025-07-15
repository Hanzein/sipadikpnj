<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\RiwayatPengajuan;

class TotalPotonganUKT extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $nim = Auth::user()->nim;

        $total = RiwayatPengajuan::where('nim', $nim)
            ->where('status', 'accepted') // Tambahkan filter status
            ->sum('nominal_apresiasi');

        return [
            Stat::make('Total Potongan UKT', 'Rp ' . number_format($total, 0, ',', '.'))
                ->color('success')
                ->description('Total apresiasi dari pengajuan yang diterima'),
        ];
    }
}