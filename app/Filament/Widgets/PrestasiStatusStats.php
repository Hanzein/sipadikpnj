<?php

namespace App\Filament\Widgets;

use App\Models\Validasi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PrestasiStatusStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Submitted', Validasi::where('status', 'submitted')->count())
            ->description('Total pengajuan belum divalidasi')
            ->color('warning'),

        Stat::make('Accepted', Validasi::where('status', 'accepted')->count())
            ->description('Total pengajuan diterima')
            ->color('success'),

        Stat::make('Rejected', Validasi::where('status', 'rejected')->count())
            ->description('Total pengajuan ditolak')
            ->color('danger'),
        ];
    }
}
