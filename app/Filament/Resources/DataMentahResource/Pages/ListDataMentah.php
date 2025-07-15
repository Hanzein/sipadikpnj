<?php

namespace App\Filament\Resources\DataMentahResource\Pages;

use App\Filament\Resources\DataMentahResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use App\Services\MooraService;
use Filament\Notifications\Notification;

class ListDataMentah extends ListRecords
{
    protected static string $resource = DataMentahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('calculate_moora')
                ->label('Hitung MOORA')
                ->icon('heroicon-o-calculator')
                ->color('primary')
                ->action(function () {
                    $mooraService = new MooraService();
                    $result = $mooraService->calculateMoora();
                    
                    Notification::make()
                        ->title('Perhitungan MOORA Berhasil!')
                        ->body('Data telah diproses dengan ' . count($result) . ' record.')
                        ->success()
                        ->send();
                        
                    redirect()->to(DataMentahResource::getUrl('index'));
                })
                ->requiresConfirmation()
                ->modalHeading('Hitung MOORA')
                ->modalDescription('Apakah Anda yakin ingin menghitung skor MOORA? Ini akan mengupdate semua peringkat yang ada.')
                ->modalSubmitActionLabel('Ya, Hitung'),
                
            // Actions\Action::make('view_statistics')
            //     ->label('Lihat Statistik')
            //     ->icon('heroicon-o-chart-bar')
            //     ->color('info')
            //     ->url(fn (): string => DataMentahResource::getUrl('statistics')),
        ];
    }
}