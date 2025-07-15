<?php

namespace App\Filament\Resources\ValidasiResource\Pages;

use App\Filament\Resources\ValidasiResource;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\BadgeEntry;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Http;

class ViewValidasi extends ViewRecord
{
    protected static string $resource = ValidasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Terima')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'accepted',
                    ]);

                    Notification::make()
                        ->title('Pengajuan berhasil diterima.')
                        ->success()
                        ->send();
                }),

            Action::make('Tolak')
                ->color('danger')
                ->form([
                    Textarea::make('komentar')
                        ->label('Komentar Penolakan')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'rejected',
                        'komentar' => $data['komentar'],
                    ]);

                    Notification::make()
                        ->title('Pengajuan ditolak dengan komentar.')
                        ->danger()
                        ->send();
                }),

                Action::make('Automation Search')
                ->color('gray')
                ->icon('heroicon-m-magnifying-glass')
                ->action(function () {
                    $namaLomba = $this->record->nama_lomba;
            
                    try {
                        $response = Http::post(env('SEARCH_API_URL') . '/search', [
                            'competition_name' => $namaLomba,
                        ]);
            
                        if ($response->successful()) {
                            $status = $response->json()['status'] ?? 'Tidak diketahui';
                            Notification::make()
                                ->title("Hasil Automation:")
                                ->body($status)
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title("Gagal melakukan automation search")
                                ->body("Kode: " . $response->status())
                                ->danger()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title("Terjadi error saat menghubungi Python service")
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema($this->getFormSchema());
    }

    public function getFormSchema(): array
    {
        return [
            // === Data dasar / identitas ===
            // Components\TextInput::make('id')
            //     ->disabled(),
            Components\TextInput::make('nim')
                ->label('NIM')
                ->disabled(),
            Components\TextInput::make('nama')
                ->label('Nama Mahasiswa')
                ->disabled(),
    
            // === Dokumen utama ===
            Components\FileUpload::make('surat_tugas')
                ->disabled()
                ->downloadable()
                ->previewable()
                ->disk('public'),
    
            Components\TextInput::make('no_surat')->disabled(),
    
            Components\FileUpload::make('sertifikat_lomba')
                ->disabled()
                ->downloadable()
                ->previewable()
                ->disk('public'),
    
            // === Info kompetisi ===
            Components\TextInput::make('nama_lomba')->disabled(),
            Components\TextInput::make('juara')->disabled(),
            Components\TextInput::make('tingkat_kompetisi')->disabled(),
            Components\TextInput::make('jumlah_peserta')->disabled(),
            Components\DatePicker::make('tanggal_pelaksanaan')->disabled(),
            Components\TextInput::make('tempat_pelaksanaan')->disabled(),
            Components\TextInput::make('lembaga_penyelenggara')->disabled(),
            Components\TextInput::make('link_kompetisi')->disabled(),
    
            // === Pembimbing ===
            Components\TextInput::make('nama_dosen')->disabled(),
            Components\TextInput::make('nip_dosen')->disabled(),
    
            // === Lampiran tambahan ===
            Components\FileUpload::make('file_peserta')
                ->disabled()
                ->downloadable()
                ->previewable()
                ->disk('public'),
    
            Components\FileUpload::make('foto_kegiatan')
                ->multiple()
                ->disabled()
                ->previewable()
                ->disk('public'),
    
            Components\FileUpload::make('foto_web_kompetisi')
                ->label('Foto Web Kompetisi')
                ->disabled()
                ->downloadable()
                ->previewable()
                ->disk('public'),
    
            // === Status & evaluasi ===
            // Components\TextInput::make('status')->disabled(),
            // Components\Textarea::make('komentar')
            //     ->disabled(),
    
            // Components\TextInput::make('score_moora')
            //     ->label('Score MOORA')
            //     ->disabled(),
    
            Components\TextInput::make('peringkat')
                ->disabled(),
    
            Components\TextInput::make('nominal_apresiasi')
                ->label('Nominal Apresiasi')
                ->disabled(),
    
            Components\TextInput::make('periode_semester')
                ->label('Periode Semester')
                ->disabled(),
    
            // === Timestamps ===
            Components\Placeholder::make('created_at')
                ->label('Dibuat')
                ->content(fn ($record) => $record->created_at?->translatedFormat('d M Y H:i')),
    
            Components\Placeholder::make('updated_at')
                ->label('Diubah Terakhir')
                ->content(fn ($record) => $record->updated_at?->translatedFormat('d M Y H:i')),
        ];
    }    
    
}
