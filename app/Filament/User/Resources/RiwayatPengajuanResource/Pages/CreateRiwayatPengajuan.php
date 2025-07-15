<?php

namespace App\Filament\User\Resources\RiwayatPengajuanResource\Pages;

use App\Filament\User\Resources\RiwayatPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\PengaturanWaktu;

class CreateRiwayatPengajuan extends CreateRecord
{
    protected static string $resource = RiwayatPengajuanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mount(): void
    {
        parent::mount();
        
        // Cek apakah periode pengajuan aktif
        if (!RiwayatPengajuanResource::isSubmissionPeriodActive()) {
            $this->handleInactivePeriod();
            return;
        }

        // Cek apakah masih bisa mengajukan
        if (!RiwayatPengajuanResource::canSubmitPengajuan()) {
            $this->handleQuotaExceeded();
            return;
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

    protected function handleQuotaExceeded(): void
    {
        $currentPeriod = PengaturanWaktu::getCurrentPeriod();
        $maxSubmissions = $currentPeriod ? $currentPeriod->batas_pengajuan : 5;
        
        Notification::make()
            ->title('Batas Pengajuan Tercapai')
            ->body("Anda telah mencapai batas maksimal $maxSubmissions pengajuan untuk periode ini.")
            ->danger()
            ->persistent()
            ->send();
        
        $this->redirect($this->getResource()::getUrl('index'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Tambahkan NIM user yang sedang login
        $data['nim'] = auth()->user()->nim;
        
        // Set status default
        $data['status'] = 'submitted';
        
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pengajuan berhasil dikirim';
    }

    protected function afterCreate(): void
    {
        $remaining = RiwayatPengajuanResource::getRemainingSubmissions();
        
        if ($remaining > 0) {
            Notification::make()
                ->title('Pengajuan Berhasil Dikirim')
                ->body("Sisa kuota pengajuan Anda: $remaining")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Pengajuan Berhasil Dikirim')
                ->body('Anda telah mencapai batas maksimal pengajuan untuk periode ini.')
                ->success()
                ->send();
        }
    }
}