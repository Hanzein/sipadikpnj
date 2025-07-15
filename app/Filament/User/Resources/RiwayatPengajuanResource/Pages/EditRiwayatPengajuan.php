<?php

namespace App\Filament\User\Resources\RiwayatPengajuanResource\Pages;

use App\Filament\User\Resources\RiwayatPengajuanResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Models\PengaturanWaktu;

class EditRiwayatPengajuan extends EditRecord
{
    protected static string $resource = RiwayatPengajuanResource::class;

    public function mount($record): void
    {
        parent::mount($record);

        // Cek apakah periode pengajuan aktif
        if (!RiwayatPengajuanResource::isSubmissionPeriodActive()) {
            $this->handleInactivePeriod();
            return;
        }

        // Hanya boleh mengedit jika statusnya "rejected"
        if ($this->record->status !== 'rejected') {
            $this->redirect(RiwayatPengajuanResource::getUrl('view', ['record' => $this->record]));
        }
    }

    protected function handleInactivePeriod(): void
    {
        $currentPeriod = PengaturanWaktu::getCurrentPeriod();
        
        if (!$currentPeriod) {
            Notification::make()
                ->title('Periode Pengajuan Tidak Aktif')
                ->body('Saat ini tidak ada periode pengajuan yang aktif.')
                ->danger()
                ->persistent()
                ->send();
        } elseif ($currentPeriod->isUpcoming()) {
            Notification::make()
                ->title('Periode Pengajuan Belum Dimulai')
                ->body("Periode pengajuan akan dimulai pada {$currentPeriod->tanggal_buka->format('d/m/Y H:i')}")
                ->warning()
                ->persistent()
                ->send();
        } else {
            Notification::make()
                ->title('Periode Pengajuan Sudah Berakhir')
                ->body("Periode pengajuan telah berakhir pada {$currentPeriod->tanggal_tutup->format('d/m/Y H:i')}")
                ->danger()
                ->persistent()
                ->send();
        }
        
        $this->redirect($this->getResource()::getUrl('index'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Components\FileUpload::make('surat_tugas')
                ->required()
                ->downloadable()
                ->previewable()
                ->disk('public'),

            Components\TextInput::make('no_surat')->required(),
            Components\FileUpload::make('sertifikat_lomba')
                ->required()
                ->downloadable()
                ->previewable()
                ->disk('public'),

            Components\TextInput::make('nama_lomba')->required(),
            Components\TextInput::make('juara')->required(),

            Components\Select::make('jumlah_peserta')
                ->label('Jumlah Peserta')
                ->options([
                    '6 Jurusan' => '6 Jurusan',
                    '10 Perguruan Tinggi' => '10 Perguruan Tinggi',
                    '1-2 Provinsi' => '1-2 Provinsi',
                    '3-4 Provinsi' => '3-4 Provinsi',
                    '5 Provinsi' => '5 Provinsi',
                    '1-2 Negara' => '1-2 Negara',
                    '3 Negara' => '3 Negara',
                ])
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    switch ($state) {
                        case '6 Jurusan':
                            $set('tingkat_kompetisi', 'Lokal');
                            break;
                        case '10 Perguruan Tinggi':
                        case '1-2 Provinsi':
                            $set('tingkat_kompetisi', 'Provinsi');
                            break;
                        case '3-4 Provinsi':
                            $set('tingkat_kompetisi', 'Wilayah');
                            break;
                        case '5 Provinsi':
                        case '1-2 Negara':
                            $set('tingkat_kompetisi', 'Nasional');
                            break;
                        case '3 Negara':
                            $set('tingkat_kompetisi', 'Internasional');
                            break;
                        default:
                            $set('tingkat_kompetisi', '');
                            break;
                    }
                }),

            Components\TextInput::make('tingkat_kompetisi')->readOnly(),

            Components\DatePicker::make('tanggal_pelaksanaan')->required(),
            Components\TextInput::make('tempat_pelaksanaan')->required(),
            Components\TextInput::make('lembaga_penyelenggara')->required(),
            Components\TextInput::make('link_kompetisi')->required(),
            Components\TextInput::make('nama_dosen')->required(),
            Components\TextInput::make('nip_dosen')->required(),

            Components\FileUpload::make('file_peserta')
                ->downloadable()
                ->previewable()
                ->disk('public'),

            Components\FileUpload::make('foto_kegiatan')
                ->required()
                ->multiple()
                ->previewable()
                ->disk('public'),
        ]);
    }

    protected function afterSave(): void
    {
        // Duplikasi data
        $newPengajuan = $this->record->replicate();
        $newPengajuan->komentar = null;
        $newPengajuan->status = 'submitted';
        $newPengajuan->created_at = now(); // reset waktu agar dianggap baru
        $newPengajuan->save();

        // Hapus data lama
        $this->record->delete();

        // Notifikasi
        Notification::make()
            ->title('Pengajuan Dikirim Ulang')
            ->body('Pengajuan baru berhasil dikirim dan menunggu validasi ulang oleh admin.')
            ->success()
            ->send();

        // Redirect ke halaman daftar pengajuan
        $this->redirect(RiwayatPengajuanResource::getUrl());
    }
}