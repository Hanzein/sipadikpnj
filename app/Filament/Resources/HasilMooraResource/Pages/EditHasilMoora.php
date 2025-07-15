<?php

namespace App\Filament\Resources\HasilMooraResource\Pages;

use App\Filament\Resources\HasilMooraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHasilMoora extends EditRecord
{
    protected static string $resource = HasilMooraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
