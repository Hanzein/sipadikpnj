<?php

namespace App\Filament\Resources\DataMentahResource\Pages;

use App\Filament\Resources\DataMentahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataMentah extends EditRecord
{
    protected static string $resource = DataMentahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
