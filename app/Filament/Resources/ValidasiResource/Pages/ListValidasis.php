<?php

namespace App\Filament\Resources\ValidasiResource\Pages;

use App\Filament\Resources\ValidasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class ListValidasis extends ListRecords
{
    protected static string $resource = ValidasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
