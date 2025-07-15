<?php

// App\Filament\Admin\Resources\PengaturanWaktuResource\Widgets\PengaturanWaktuStatsWidget.php

namespace App\Filament\Resources\PengaturanWaktuResource\Widgets;

use App\Models\PengaturanWaktu;
use App\Models\RiwayatPengajuan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PengaturanWaktuStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $currentPeriod = PengaturanWaktu::getCurrentPeriod();
        $totalPeriods = PengaturanWaktu::count();
        $activePeriods = PengaturanWaktu::where('is_active', true)->count();
        
        $stats = [
            Stat::make('Total Periode', $totalPeriods)
                ->description('Jumlah periode yang telah dibuat')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            
            Stat::make('Periode Aktif', $activePeriods)
                ->description('Periode yang sedang aktif')
                ->descriptionIcon('heroicon-m-play')
                ->color($activePeriods > 0 ? 'success' : 'danger'),
        ];

        if ($currentPeriod) {
            // Hitung pengajuan di periode aktif
            $currentSubmissions = RiwayatPengajuan::whereBetween('created_at', [
                $currentPeriod->tanggal_buka,
                $currentPeriod->tanggal_tutup
            ])->count();

            $stats[] = Stat::make('Pengajuan Periode Ini', $currentSubmissions)
                ->description('Total pengajuan di periode aktif')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info');

            // Status periode
            if ($currentPeriod->isCurrentlyActive()) {
                $remainingTime = $currentPeriod->getRemainingTime();
                $stats[] = Stat::make('Status Periode', 'Aktif')
                    ->description($remainingTime ? "Berakhir dalam $remainingTime" : 'Sedang berlangsung')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('success');
            } elseif ($currentPeriod->isUpcoming()) {
                $startsIn = $currentPeriod->tanggal_buka->diffForHumans();
                $stats[] = Stat::make('Status Periode', 'Akan Dimulai')
                    ->description("Dimulai $startsIn")
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('warning');
            } else {
                $stats[] = Stat::make('Status Periode', 'Berakhir')
                    ->description('Periode telah berakhir')
                    ->descriptionIcon('heroicon-m-x-circle')
                    ->color('danger');
            }
        } else {
            $stats[] = Stat::make('Status Periode', 'Tidak Ada')
                ->description('Belum ada periode aktif')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('gray');
        }

        return $stats;
    }
}