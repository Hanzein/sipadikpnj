<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratTugasResource\Pages;
use App\Filament\Resources\SuratTugasResource\RelationManagers;
use App\Models\SuratTugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuratTugasResource extends Resource
{
    protected static ?string $model = SuratTugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_surat')
                ->required()
                ->label('Nomor Surat'),
    
            Forms\Components\DatePicker::make('tanggal')
                ->required()
                ->label('Tanggal'),
    
            // Forms\Components\TextInput::make('perihal')
            //     ->required()
            //     ->label('Perihal'),
    
            Forms\Components\FileUpload::make('surat_tugas') 
                ->disk('public')
                ->required()
                ->label('File Surat')
                ->directory('surat-tugas')
                ->preserveFilenames()
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'])
                ->previewable()
                // ->downloadable()
                // ->openable(),
            ]);
    }
 
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_surat')->label('Nomor Surat'),
                Tables\Columns\TextColumn::make('tanggal')->label('Tanggal')->date(),
                // Tables\Columns\TextColumn::make('perihal')->label('Perihal'),
                // Tables\Columns\TextColumn::make('surat_tugas')->label('File'),
                Tables\Columns\TextColumn::make('surat_tugas')
                ->label('File')
                ->url(fn ($record) => asset('storage/surat-tugas/' . $record->nama_file), true)
                ->openUrlInNewTab(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSuratTugas::route('/'),
            'create' => Pages\CreateSuratTugas::route('/create'),
            'edit' => Pages\EditSuratTugas::route('/{record}/edit'),
        ];
    }
}
