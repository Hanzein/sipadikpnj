<?php

namespace App\Filament\Resources\HasilMooraResource\Pages;

use App\Filament\Resources\HasilMooraResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewHasilMoora extends ViewRecord
{
    protected static string $resource = HasilMooraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back_to_list')
                ->label('Kembali ke Daftar')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => HasilMooraResource::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return 'View Hasil Moora';
    }
}
