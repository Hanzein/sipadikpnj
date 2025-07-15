<?php

namespace App\Filament\Resources\DataMentahResource\Pages;

use App\Filament\Resources\DataMentahResource;
use Filament\Resources\Pages\ViewRecord;

class ViewDataMentah extends ViewRecord
{
    protected static string $resource = DataMentahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tidak menampilkan tombol edit
        ];
    }

    public function getTitle(): string
    {
        return 'View Data Mentah';
    }
}
