<?php

// App\Filament\User\Resources\RiwayatPengajuanResource\Widgets\PengajuanStatsWidget.php

namespace App\Filament\User\Resources\RiwayatPengajuanResource\Widgets;

use App\Models\PengaturanWaktu;
use App\Models\RiwayatPengajuan;
use App\Filament\User\Resources\RiwayatPengajuanResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PengajuanStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $currentPeriod = PengaturanWaktu::getCurrentPeriod();
        $periodStatus = RiwayatPengajuanResource::getPeriodStatus();
        
        if (!$currentPeriod) {
            return [
                Stat::make('Status Periode', 'Tidak Ada Periode Aktif')
                    ->description('Hubungi admin untuk informasi lebih lanjut')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('gray'),
            ];
        }

        $maxSubmissions = $currentPeriod->batas_pengajuan;
        $remaining = RiwayatPengajuanResource::getRemainingSubmissions();
        $used = $maxSubmissions - $remaining;

        [$periodStart, $periodEnd] = RiwayatPengajuanResource::getCurrentPeriodRange();
        
        // Statistik pengajuan user
        $userSubmissions = RiwayatPengajuan::where('nim', auth()->user()->nim)
            ->whereBetween('created_at', [$periodStart, $periodEnd]);

        $totalSubmissions = $userSubmissions->count();
        $acceptedSubmissions = $userSubmissions->where('status', 'accepted')->count();
        $rejectedSubmissions = $userSubmissions->where('status', 'rejected')->count();
        $pendingSubmissions = $userSubmissions->where('status', 'submitted')->count();

        $stats = [
            Stat::make('Kuota Pengajuan', "$used/$maxSubmissions")
                ->description($remaining > 0 ? "Sisa $remaining pengajuan" : 'Kuota habis')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($remaining > 0 ? 'success' : 'danger')
                ->chart($remaining > 0 ? [$used, $remaining] : [$maxSubmissions]),
        ];

        if ($totalSubmissions > 0) {
            $stats[] = Stat::make('Pengajuan Diterima', $acceptedSubmissions)
                ->description("Dari $totalSubmissions pengajuan")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success');

            $stats[] = Stat::make('Menunggu Review', $pendingSubmissions)
                ->description('Sedang dalam proses review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning');

            if ($rejectedSubmissions > 0) {
                $stats[] = Stat::make('Pengajuan Ditolak', $rejectedSubmissions)
                    ->description('Dapat diedit dan diajukan ulang')
                    ->descriptionIcon('heroicon-m-x-circle')
                    ->color('danger');
            }
        }

        // Status periode
        $statusStat = Stat::make('Status Periode', $periodStatus['message'])
            ->descriptionIcon(match($periodStatus['status']) {
                'aktif' => 'heroicon-m-check-circle',
                'akan_dimulai' => 'heroicon-m-clock',
                'berakhir' => 'heroicon-m-x-circle',
                'tidak_ada_periode' => 'heroicon-m-exclamation-triangle',
            })
            ->color($periodStatus['color']);

        if (isset($periodStatus['remaining_time'])) {
            $statusStat->description($periodStatus['remaining_time']);
        }

        array_unshift($stats, $statusStat);

        return $stats;
    }
}