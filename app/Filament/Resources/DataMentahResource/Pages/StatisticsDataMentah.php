<?php

namespace App\Filament\Resources\DataMentahResource\Pages;

use App\Filament\Resources\DataMentahResource;
use App\Services\MooraService;
use Filament\Resources\Pages\Page;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;

class StatisticsDataMentah extends Page implements HasInfolists
{
    use InteractsWithInfolists;

    protected static string $resource = DataMentahResource::class;
    protected static string $view = 'filament.admin.resources.pages.statistik';

    public function getTitle(): string
    {
        return 'Statistik Data Mentah';
    }

    public function statisticsInfolist(Infolist $infolist): Infolist
    {
        $mooraService = new MooraService();
        $statistics = $mooraService->getStatistics();

        return $infolist
            ->record((object) $statistics)
            ->schema([
                Section::make('Ringkasan Data')
                    ->schema([
                        TextEntry::make('score_moora')
                            ->label('Total Data')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('total_nominal')
                            ->label('Total Nominal Apresiasi')
                            ->money('IDR', true)
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(2),

                Section::make('Distribusi Tingkat Kompetisi')
                    ->schema([
                        TextEntry::make('tingkat_kompetisi')
                            ->label('Tingkat Kompetisi')
                            ->formatStateUsing(function ($state) {
                                $output = [];
                                foreach ($state as $tingkat => $count) {
                                    $output[] = $tingkat . ': ' . $count . ' data';
                                }
                                return implode("\n", $output);
                            })
                            ->html(),
                    ]),

                Section::make('Distribusi Juara')
                    ->schema([
                        TextEntry::make('juara')
                            ->label('Juara')
                            ->formatStateUsing(function ($state) {
                                $output = [];
                                foreach ($state as $juara => $count) {
                                    $output[] = $juara . ': ' . $count . ' data';
                                }
                                return implode("\n", $output);
                            })
                            ->html(),
                    ]),

                Section::make('Distribusi Jumlah Peserta')
                    ->schema([
                        TextEntry::make('jumlah_peserta')
                            ->label('Jumlah Peserta')
                            ->formatStateUsing(function ($state) {
                                $output = [];
                                foreach ($state as $peserta => $count) {
                                    $output[] = $peserta . ': ' . $count . ' data';
                                }
                                return implode("\n", $output);
                            })
                            ->html(),
                    ]),
            ]);
    }

    public function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(DataMentahResource::getUrl('index')),
        ];
    }
}