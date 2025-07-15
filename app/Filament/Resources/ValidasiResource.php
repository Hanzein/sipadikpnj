<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ValidasiResource\Pages;
use App\Filament\Resources\ValidasiResource\RelationManagers;
use App\Models\Validasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ValidasiResource extends Resource
{
    protected static ?string $model = Validasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getLabel(): string
    {
        return 'Validasi Prestasi';
    }

    public static function getPluralLabel(): string
    {
        return 'Validasi Prestasi';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([ 
                TextColumn::make('nama')->label('Nama Mahasiswa')  
                ->searchable()
                ->sortable(),
                TextColumn::make('nama_lomba')->label('Nama Lomba')
                ->searchable(),
                TextColumn::make('juara'),
                TextColumn::make('user.prodi')
                ->label('Prodi')
                ->searchable()
                ->sortable(),

            TextColumn::make('user.jurusan')
                ->label('Jurusan')
                ->searchable()
                ->sortable(),
                TextColumn::make('tingkat_kompetisi'),
                TextColumn::make('tanggal_pelaksanaan')->date(),
                TextColumn::make('nama_dosen'),
                TextColumn::make('status')->badge()
                ->color(fn ($state) => match ($state) {
                    'submitted' => 'warning',
                    'accepted' => 'success',
                    'rejected' => 'danger',
                }),
            ])
            ->filters([
                SelectFilter::make('tingkat_kompetisi')
                ->label('Tingkat Kompetisi')
                ->options([
                    'Internasional' => 'Internasional',
                    'Nasional' => 'Nasional',
                    'Provinsi' => 'Provinsi',
                    'Wilayah' => 'Wilayah',
                    'Lokal' => 'Lokal',
                ]),

            SelectFilter::make('nama_lomba')
                ->label('Nama Lomba')
                ->options(fn () => Validasi::query()
                    ->distinct()
                    ->pluck('nama_lomba', 'nama_lomba')
                    ->filter()),

            SelectFilter::make('juara')
                ->label('Juara')
                ->options(fn () => Validasi::query()
                    ->distinct()
                    ->pluck('juara', 'juara')
                    ->filter()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(), // Untuk tombol lihat detail
                Tables\Actions\EditAction::make(), 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListValidasis::route('/'),
            'edit' => Pages\EditValidasi::route('/{record}/edit'),
            'view' => Pages\ViewValidasi::route('/{record}'),
        ];
    }
}
