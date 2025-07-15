<?php

namespace App\Filament\Resources;

use App\Models\RiwayatPengajuan;
use App\Services\MooraService;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Filament\Resources\DataMentahResource\Pages;

class DataMentahResource extends Resource
{
    protected static ?string $model = RiwayatPengajuan::class;
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Data Mentah';
    protected static ?string $pluralModelLabel = 'Data Mentah';
    protected static ?string $navigationGroup = 'Perhitungan';
    protected static ?int $navigationSort = 0;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->orderBy('created_at', 'desc');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->headerActions([
            ])
            ->columns([
                Tables\Columns\TextColumn::make('row_number')
                    ->label('No')
                    ->state(function ($record, $livewire) {
                        static $index = 0;
                        return ++$index;
                    }),
                    
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('nama_lomba')
                    ->label('Nama Lomba')
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('tingkat_kompetisi')
                    ->label('Tingkat Kompetisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Internasional' => 'danger',
                        'Nasional' => 'warning',
                        'Wilayah' => 'info',
                        'Provinsi' => 'primary',
                        'Lokal' => 'gray',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('juara')
                    ->label('Juara')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Juara 1' => 'success',
                        'Juara 2' => 'info',
                        'Juara 3' => 'warning',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('jumlah_peserta')
                    ->label('Jumlah Peserta')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('bobot_tingkat')
                    ->label('Bobot Tingkat')
                    ->state(function ($record) {
                        $bobot = [
                            'Internasional' => 5,
                            'Nasional' => 4,
                            'Wilayah' => 3,
                            'Provinsi' => 2,
                            'Lokal' => 1,
                        ];
                        return $bobot[ucfirst(strtolower($record->tingkat_kompetisi))] ?? 0;
                    })
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('bobot_juara')
                    ->label('Bobot Juara')
                    ->state(function ($record) {
                        $bobot = [
                            'Juara 1' => 3,
                            'Juara 2' => 2,
                            'Juara 3' => 1,
                        ];
                        // Normalize juara first
                        $juara = $record->juara;
                        $map = [
                            '1st winner' => 'Juara 1',
                            'first winner' => 'Juara 1',
                            'juara i' => 'Juara 1',
                            'juara 1' => 'Juara 1',
                            '2nd winner' => 'Juara 2',
                            'second winner' => 'Juara 2',
                            'juara ii' => 'Juara 2',
                            'juara 2' => 'Juara 2',
                            '3rd winner' => 'Juara 3',
                            'third winner' => 'Juara 3',
                            'juara iii' => 'Juara 3',
                            'juara 3' => 'Juara 3',
                        ];
                        $normalizedJuara = $map[strtolower(trim($juara))] ?? $juara;
                        return $bobot[$normalizedJuara] ?? 0;
                    })
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('bobot_peserta')
                    ->label('Bobot Peserta')
                    ->state(function ($record) {
                        $bobot = [
                            '6 Jurusan' => 1,
                            '10 Perguruan Tinggi' => 2,
                            '1-2 Provinsi' => 3,
                            '3-4 Provinsi' => 4,
                            '5 Provinsi' => 5,
                            '1-2 Negara' => 6,
                            '3 Negara' => 7,
                        ];
                        return $bobot[$record->jumlah_peserta] ?? 0;
                    })
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tingkat_kompetisi')
                    ->label('Tingkat Kompetisi')
                    ->options([
                        'Internasional' => 'Internasional',
                        'Nasional' => 'Nasional',
                        'Wilayah' => 'Wilayah',
                        'Provinsi' => 'Provinsi',
                        'Lokal' => 'Lokal',
                    ]),
                    
                Tables\Filters\SelectFilter::make('juara')
                    ->label('Juara')
                    ->options([
                        'Juara 1' => 'Juara 1',
                        'Juara 2' => 'Juara 2',
                        'Juara 3' => 'Juara 3',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Mahasiswa')
                    ->schema([
                        TextEntry::make('nama')
                            ->label('Nama Mahasiswa'),
                        TextEntry::make('created_at')
                            ->label('Tanggal Pengajuan')
                            ->dateTime('d-m-Y H:i:s'),
                    ])
                    ->columns(2),
                    
                Section::make('Detail Kompetisi')
                    ->schema([
                        TextEntry::make('nama_lomba')
                            ->label('Nama Lomba'),
                        TextEntry::make('tingkat_kompetisi')
                            ->label('Tingkat Kompetisi')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Internasional' => 'danger',
                                'Nasional' => 'warning',
                                'Wilayah' => 'info',
                                'Provinsi' => 'primary',
                                'Lokal' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('juara')
                            ->label('Juara')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Juara 1' => 'success',
                                'Juara 2' => 'info',
                                'Juara 3' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('jumlah_peserta')
                            ->label('Jumlah Peserta'),
                    ])
                    ->columns(2),
                    
                Section::make('Bobot Kriteria')
                    ->schema([
                        TextEntry::make('bobot_tingkat')
                            ->label('Bobot Tingkat Kompetisi')
                            ->state(function ($record) {
                                $bobot = [
                                    'Internasional' => 5,
                                    'Nasional' => 4,
                                    'Wilayah' => 3,
                                    'Provinsi' => 2,
                                    'Lokal' => 1,
                                ];
                                return $bobot[ucfirst(strtolower($record->tingkat_kompetisi))] ?? 0;
                            }),
                        TextEntry::make('bobot_juara')
                            ->label('Bobot Juara')
                            ->state(function ($record) {
                                $bobot = [
                                    'Juara 1' => 3,
                                    'Juara 2' => 2,
                                    'Juara 3' => 1,
                                ];
                                return $bobot[$record->juara] ?? 0;
                            }),
                        TextEntry::make('bobot_peserta')
                            ->label('Bobot Jumlah Peserta')
                            ->state(function ($record) {
                                $bobot = [
                                    '6 Jurusan' => 1,
                                    '10 Perguruan Tinggi' => 2,
                                    '1-2 Provinsi' => 3,
                                    '3-4 Provinsi' => 4,
                                    '5 Provinsi' => 5,
                                    '1-2 Negara' => 6,
                                    '3 Negara' => 7,
                                ];
                                return $bobot[$record->jumlah_peserta] ?? 0;
                            }),
                    ])
                    ->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataMentah::route('/'),
            'view' => Pages\ViewDataMentah::route('/{record}'),
            'statistics' => Pages\StatisticsDataMentah::route('/statistik'),
        ];
    }
}