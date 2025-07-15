<?php

namespace App\Filament\Resources\PengaturanWaktuResource\Pages;

use App\Filament\Resources\PengaturanWaktuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengaturanWaktu extends EditRecord
{
    protected static string $resource = PengaturanWaktuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
