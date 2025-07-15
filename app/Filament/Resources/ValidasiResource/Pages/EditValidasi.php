<?php

namespace App\Filament\Resources\ValidasiResource\Pages;

use App\Filament\Resources\ValidasiResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components;

class EditValidasi extends EditRecord
{
    protected static string $resource = ValidasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            // === Identitas & dasar ===
            // Components\TextInput::make('id')
            //     ->disabled(),
            Components\TextInput::make('nim')
                ->label('NIM')
                ->required(),
            Components\TextInput::make('nama')
                ->label('Nama Mahasiswa')
                ->required(),
    
            // === Dokumen utama ===
            Components\FileUpload::make('surat_tugas')
                ->downloadable()
                ->previewable()
                ->disk('public')
                ->required(),
    
            Components\TextInput::make('no_surat')->required(),
    
            Components\FileUpload::make('sertifikat_lomba')
                ->downloadable()
                ->previewable()
                ->disk('public')
                ->required(),
    
            // === Info kompetisi ===
            Components\TextInput::make('nama_lomba')->required(),
            Components\TextInput::make('juara')->required(),
            Components\TextInput::make('tingkat_kompetisi')->required(),
            Components\TextInput::make('jumlah_peserta')->required(),
            Components\DatePicker::make('tanggal_pelaksanaan')->required(),
            Components\TextInput::make('tempat_pelaksanaan')->required(),
            Components\TextInput::make('lembaga_penyelenggara')->required(),
            Components\TextInput::make('link_kompetisi')->required(),
    
            // === Pembimbing ===
            Components\TextInput::make('nama_dosen')->required(),
            Components\TextInput::make('nip_dosen')->required(),
    
            // === Lampiran tambahan ===
            Components\FileUpload::make('file_peserta')
                ->downloadable()
                ->previewable()
                ->disk('public'),
    
            Components\FileUpload::make('foto_kegiatan')
                ->multiple()
                ->downloadable()
                ->previewable()
                ->disk('public')
                ->required(),
    
            Components\FileUpload::make('foto_web_kompetisi')
                ->label('Foto Web Kompetisi')
                ->downloadable()
                ->previewable()
                ->disk('public'),
    
            // === Status & evaluasi ===
            Components\Select::make('status')
                ->options([
                    'submitted' => 'Submitted',
                    'accepted'  => 'Accepted',
                    'rejected'  => 'Rejected',
                ])
                ->required(),
    
            Components\Textarea::make('komentar')
                ->label('Komentar (jika ditolak)'),
    
            // Components\TextInput::make('score_moora')
            //     ->label('Score MOORA')
            //     ->numeric()
            //     ->step('0.0001'),
    
            // Components\TextInput::make('peringkat')
            //     ->numeric(),
    
            Components\TextInput::make('nominal_apresiasi')
                ->label('Nominal Apresiasi')
                ->numeric()
                ->prefix('Rp'),
    
            Components\TextInput::make('periode_semester')
                ->label('Periode Semester'),
    
            // === Timestamps (readonly) ===
            Components\Placeholder::make('created_at')
                ->label('Dibuat')
                ->content(fn ($record) => $record->created_at?->translatedFormat('d M Y H:i')),
    
            Components\Placeholder::make('updated_at')
                ->label('Diubah Terakhir')
                ->content(fn ($record) => $record->updated_at?->translatedFormat('d M Y H:i')),
        ]);
    }
    
}
