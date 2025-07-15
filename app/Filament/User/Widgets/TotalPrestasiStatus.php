<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\RiwayatPengajuan;

class TotalPrestasiStatus extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $nim = Auth::user()->nim;

        $accepted = RiwayatPengajuan::where('nim', $nim)
            ->where('status', 'accepted')
            ->count();

        $submitted = RiwayatPengajuan::where('nim', $nim)
            ->where('status', 'submitted')
            ->count();

        $rejected = RiwayatPengajuan::where('nim', $nim)
            ->where('status', 'rejected')
            ->count();

        return [
            Stat::make('Prestasi Diterima', $accepted)
                ->color('success')
                ->description('Pengajuan yang sudah diterima'),

            Stat::make('Menunggu Review', $submitted)
                ->color('warning')
                ->description('Pengajuan yang sedang diproses'),

            Stat::make('Prestasi Ditolak', $rejected)
                ->color('danger')
                ->description('Pengajuan yang ditolak'),
        ];
    }
}