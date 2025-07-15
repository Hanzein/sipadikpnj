<?php

namespace App\Filament\User\Resources\RiwayatPengajuanResource\Pages;

use App\Filament\User\Resources\RiwayatPengajuanResource;
use App\Livewire\FormPengajuanUKT;
// use App\Filament\User\Traits\CanSubmitBantuanUkt;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Livewire\Attributes\On;

class ListRiwayatPengajuan extends ListRecords
{
    protected static string $resource = RiwayatPengajuanResource::class;
    
    // use CanSubmitBantuanUkt;

    protected $listeners = ['pengajuan-created' => 'refreshPage'];

    public function getView(): string
    {
        return 'filament.user.resources.user-resource.pages.list-riwayat-pengajuan';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Kosongkan array karena tombol akan ditangani oleh Livewire component
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RiwayatPengajuanResource\Widgets\PengajuanStatsWidget::class,
        ];
    }

    #[On('pengajuan-created')]
    public function refreshPage(): void
    {
        // Refresh halaman setelah pengajuan berhasil dibuat
        $this->redirect($this->getResource()::getUrl('index'));
    }
}