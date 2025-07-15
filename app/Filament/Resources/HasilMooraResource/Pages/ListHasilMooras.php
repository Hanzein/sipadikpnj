<?php

namespace App\Filament\Resources\HasilMooraResource\Pages;

use App\Filament\Resources\HasilMooraResource;
use App\Services\MooraService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use App\Exports\HasilMooraExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class ListHasilMooras extends ListRecords
{
    protected static string $resource = HasilMooraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Hitung Ulang Moora')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    (new MooraService())->calculateMoora();

                    Notification::make()
                        ->title('Perhitungan ulang MOORA berhasil!')
                        ->success()
                        ->send();
                }),

                Actions\Action::make('view_raw_data')
                ->label('Lihat Data Mentah')
                ->icon('heroicon-o-table-cells')
                ->color('info')
                ->url(fn (): string => \App\Filament\Resources\DataMentahResource::getUrl('index')),

                Actions\Action::make('Export ke Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new HasilMooraExport, 'hasil-moora.xlsx');
                }),
                
        ];
    }
}
