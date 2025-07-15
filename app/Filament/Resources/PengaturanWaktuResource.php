<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengaturanWaktuResource\Pages;
use App\Filament\Resources\PengaturanWaktuResource\RelationManagers;
use App\Models\PengaturanWaktu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class PengaturanWaktuResource extends Resource
{
    protected static ?string $model = PengaturanWaktu::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Pengaturan Waktu';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 1;

    public static function getLabel(): string
    {
        return 'Pengaturan Waktu';
    }

    public static function getPluralLabel(): string
    {
        return 'Pengaturan Waktu';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Periode')
                    ->schema([
                        Forms\Components\TextInput::make('nama_periode')
                            ->label('Nama Periode')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Pengajuan Prestasi Semester Ganjil 2024/2025'),
                        
                        Forms\Components\Select::make('semester')
                            ->label('Semester')
                            ->options([
                                'ganjil' => 'Ganjil',
                                'genap' => 'Genap',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('tahun_akademik')
                            ->label('Tahun Akademik')
                            ->required()
                            ->placeholder('2024/2025')
                            ->helperText('Format: YYYY/YYYY'),
                        
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->placeholder('Deskripsi periode pengajuan...')
                            ->rows(3),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pengaturan Waktu')
                    ->schema([
                        Forms\Components\DateTimePicker::make('tanggal_buka')
                            ->label('Tanggal & Waktu Buka')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Tanggal dan waktu mulai pengajuan dibuka'),
                        
                        Forms\Components\DateTimePicker::make('tanggal_tutup')
                            ->label('Tanggal & Waktu Tutup')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Tanggal dan waktu pengajuan ditutup')
                            ->after('tanggal_buka'),
                        
                        Forms\Components\TextInput::make('batas_pengajuan')
                            ->label('Batas Pengajuan per Mahasiswa')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->minValue(1)
                            ->maxValue(20)
                            ->helperText('Maksimal jumlah pengajuan per mahasiswa dalam periode ini'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->helperText('Hanya satu periode yang dapat aktif dalam satu waktu')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_periode')
                    ->label('Nama Periode')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ganjil' => 'info',
                        'genap' => 'success',
                    }),
                
                Tables\Columns\TextColumn::make('tahun_akademik')
                    ->label('Tahun Akademik')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tanggal_buka')
                    ->label('Tanggal Buka')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tanggal_tutup')
                    ->label('Tanggal Tutup')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('batas_pengajuan')
                    ->label('Batas Pengajuan')
                    ->alignCenter(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Periode')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'akan_dimulai' => 'warning',
                        'berakhir' => 'danger',
                        'nonaktif' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Sedang Berlangsung',
                        'akan_dimulai' => 'Akan Dimulai',
                        'berakhir' => 'Sudah Berakhir',
                        'nonaktif' => 'Tidak Aktif',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('semester')
                    ->options([
                        'ganjil' => 'Ganjil',
                        'genap' => 'Genap',
                    ]),
                
                Tables\Filters\Filter::make('is_active')
                    ->label('Hanya Aktif')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),
                
                Tables\Filters\Filter::make('current_period')
                    ->label('Periode Berlangsung')
                    ->query(fn (Builder $query): Builder => $query->current()),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (PengaturanWaktu $record) {
                        // Nonaktifkan periode lain
                        PengaturanWaktu::where('id', '!=', $record->id)
                            ->update(['is_active' => false]);
                        
                        // Aktifkan periode ini
                        $record->update(['is_active' => true]);
                        
                        Notification::make()
                            ->title('Periode berhasil diaktifkan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PengaturanWaktu $record): bool => !$record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Aktifkan Periode')
                    ->modalDescription('Apakah Anda yakin ingin mengaktifkan periode ini? Periode lain yang sedang aktif akan dinonaktifkan.'),
                
                Tables\Actions\Action::make('deactivate')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-pause')
                    ->color('danger')
                    ->action(function (PengaturanWaktu $record) {
                        $record->update(['is_active' => false]);
                        
                        Notification::make()
                            ->title('Periode berhasil dinonaktifkan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PengaturanWaktu $record): bool => $record->is_active)
                    ->requiresConfirmation(),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengaturanWaktu::route('/'),
            'create' => Pages\CreatePengaturanWaktu::route('/create'),
            'edit' => Pages\EditPengaturanWaktu::route('/{record}/edit'),
        ];
    }
}