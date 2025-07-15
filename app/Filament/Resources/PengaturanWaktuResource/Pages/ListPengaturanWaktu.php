<?php

namespace App\Filament\Resources\PengaturanWaktuResource\Pages;

use App\Filament\Resources\PengaturanWaktuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengaturanWaktu extends ListRecords
{
    protected static string $resource = PengaturanWaktuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PengaturanWaktuResource\Widgets\PengaturanWaktuStatsWidget::class,
        ];
    }
}