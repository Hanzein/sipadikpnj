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
use App\Filament\Resources\HasilMooraResource\Pages;

class HasilMooraResource extends Resource
{
    protected static ?string $model = RiwayatPengajuan::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Hasil MOORA';
    protected static ?string $pluralModelLabel = 'Hasil MOORA';
    protected static ?string $navigationGroup = 'Perhitungan';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('score_moora') // Hanya tampilkan yang sudah dihitung
            ->orderBy('peringkat', 'asc')
            ->orderBy('created_at', 'asc'); // Untuk handle duplicate ranking
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->headerActions([
            ])
            ->columns([
                Tables\Columns\TextColumn::make('peringkat')
                    ->label('Peringkat')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(function ($state) {
                        if ($state <= 3) return 'success';
                        if ($state <= 10) return 'warning';
                        return 'gray';
                    })
                    ->formatStateUsing(function ($state, $record) {
                        // Handle duplicate ranking display
                        $sameScoreCount = RiwayatPengajuan::where('peringkat', $state)
                            ->where('score_moora', $record->score_moora)
                            ->count();
                        
                        if ($sameScoreCount > 1) {
                            $position = RiwayatPengajuan::where('peringkat', $state)
                                ->where('score_moora', $record->score_moora)
                                ->where('created_at', '<=', $record->created_at)
                                ->count();
                            return $state . '.' . $position;
                        }
                        
                        return $state;
                    }),
                    
                Tables\Columns\TextColumn::make('periode_semester')
                    ->label('Periode Semester')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('nama_lomba')
                    ->label('Nama Lomba')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->nama_lomba;
                    }),
                    
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
                    
                Tables\Columns\TextColumn::make('score_moora')
                    ->label('Skor MOORA')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => number_format($state, 6))
                    ->sortable()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('nominal_apresiasi')
                    ->label('Nominal Apresiasi')
                    ->money('IDR', true)
                    ->alignEnd()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('periode_semester')
                    ->label('Periode Semester')
                    ->options(function () {
                        // Ambil data periode semester yang unik dari database
                        $periods = RiwayatPengajuan::whereNotNull('periode_semester')
                            ->whereNotNull('score_moora')
                            ->distinct()
                            ->pluck('periode_semester', 'periode_semester')
                            ->sort()
                            ->toArray();
                        
                        return $periods;
                    })
                    ->placeholder('Semua Periode'),
                    
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
                    
                Tables\Filters\Filter::make('top_10')
                    ->label('Top 10')
                    ->query(fn ($query) => $query->where('peringkat', '<=', 10))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('top_20')
                    ->label('Top 20')
                    ->query(fn ($query) => $query->where('peringkat', '<=', 20))
                    ->toggle(),
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
                Section::make('Peringkat dan Skor')
                    ->schema([
                        TextEntry::make('peringkat')
                            ->label('Peringkat')
                            ->badge()
                            ->color(function ($state) {
                                if ($state <= 3) return 'success';
                                if ($state <= 10) return 'warning';
                                return 'gray';
                            }),
                        TextEntry::make('score_moora')
                            ->label('Skor MOORA')
                            ->formatStateUsing(fn ($state) => number_format($state, 6)),
                        TextEntry::make('nominal_apresiasi')
                            ->label('Nominal Apresiasi')
                            ->money('IDR', true),
                    ])
                    ->columns(3),
                    
                Section::make('Informasi Mahasiswa')
                    ->schema([
                        TextEntry::make('nama')
                            ->label('Nama Mahasiswa'),
                        TextEntry::make('periode_semester')
                            ->label('Periode Semester')
                            ->badge()
                            ->color('info'),
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
                    
                Section::make('Komponen Skor MOORA')
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
            'index' => Pages\ListHasilMooras::route('/'),
            'view' => Pages\ViewHasilMoora::route('/{record}'),
        ];
    }
}