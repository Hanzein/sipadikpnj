<?php
namespace App\Filament\User\Resources\RiwayatPengajuanResource\Pages;
use App\Filament\User\Resources\RiwayatPengajuanResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\BadgeEntry;
use Filament\Forms\Components\Placeholder;
use Carbon\Carbon;

class ViewDetailPengajuan extends ViewRecord
{
    protected static string $resource = RiwayatPengajuanResource::class;

    public function form(Form $form): Form
    {
        return $form->schema($this->getFormSchema());
    }

    // Method untuk mendapatkan periode semester berdasarkan tanggal created_at
    public function getSemesterPeriod(): string
    {
        $createdAt = $this->record->created_at;
        $year = $createdAt->year;
        $month = $createdAt->month;
        
        // Tentukan semester berdasarkan bulan
        // Semester Ganjil: Agustus - Januari (bulan 8-12 dan 1)
        // Semester Genap: Februari - Juli (bulan 2-7)
        if ($month >= 8 || $month == 1) {
            // Semester Ganjil
            $academicYear = $month == 1 ? $year - 1 : $year;
            return "Semester Ganjil " . $academicYear . "/" . ($academicYear + 1);
        } else {
            // Semester Genap
            return "Semester Genap " . $year . "/" . ($year + 1);
        }
    }

    public function getFormSchema(): array
    {
        return [
            // Informasi Periode Semester
            Components\Section::make('Informasi Periode')
                ->schema([
                    Components\Placeholder::make('periode_semester')
                        ->label('Periode Semester')
                        ->content(fn () => $this->getSemesterPeriod()),
                    Components\Placeholder::make('tanggal_pengajuan')
                        ->label('Tanggal Pengajuan')
                        ->content(fn () => $this->record->created_at->format('d F Y H:i')),
                    Components\Placeholder::make('status_pengajuan')
                        ->label('Status Pengajuan')
                        ->content(function () {
                            $status = $this->record->status;
                            $statusLabels = [
                                'submitted' => 'Menunggu Verifikasi',
                                'accepted' => 'Diterima',
                                'rejected' => 'Ditolak'
                            ];
                            return $statusLabels[$status] ?? $status;
                        }),
                ])
                ->columns(3),

            // Informasi Dokumen
            Components\Section::make('Dokumen Pengajuan')
                ->schema([
                    Components\FileUpload::make('surat_tugas')
                        ->label('Surat Tugas')
                        ->disabled()
                        ->downloadable()
                        ->previewable()
                        ->disk('public'),
                    Components\TextInput::make('no_surat')
                        ->label('Nomor Surat')
                        ->disabled(),
                    Components\FileUpload::make('sertifikat_lomba')
                        ->label('Sertifikat Lomba')
                        ->disabled()
                        ->downloadable()
                        ->previewable()
                        ->disk('public'),
                ])
                ->columns(2),

            // Informasi Lomba
            Components\Section::make('Detail Lomba')
                ->schema([
                    Components\TextInput::make('nama_lomba')
                        ->label('Nama Lomba')
                        ->disabled(),
                    Components\TextInput::make('juara')
                        ->label('Juara')
                        ->disabled(),
                    Components\TextInput::make('tingkat_kompetisi')
                        ->label('Tingkat Kompetisi')
                        ->disabled(),
                    Components\TextInput::make('jumlah_peserta')
                        ->label('Jumlah Peserta')
                        ->disabled(),
                    Components\DatePicker::make('tanggal_pelaksanaan')
                        ->label('Tanggal Pelaksanaan')
                        ->disabled(),
                    Components\TextInput::make('tempat_pelaksanaan')
                        ->label('Tempat Pelaksanaan')
                        ->disabled(),
                    Components\TextInput::make('lembaga_penyelenggara')
                        ->label('Lembaga Penyelenggara')
                        ->disabled(),
                    Components\TextInput::make('link_kompetisi')
                        ->label('Link Kompetisi')
                        ->disabled(),
                ])
                ->columns(2),

            // Informasi Dosen Pembimbing
            Components\Section::make('Dosen Pembimbing')
                ->schema([
                    Components\TextInput::make('nama_dosen')
                        ->label('Nama Dosen')
                        ->disabled(),
                    Components\TextInput::make('nip_dosen')
                        ->label('NIP Dosen')
                        ->disabled(),
                ])
                ->columns(2),

            // File Pendukung
            Components\Section::make('File Pendukung')
                ->schema([
                    Components\FileUpload::make('file_peserta')
                        ->label('File Peserta')
                        ->disabled()
                        ->downloadable()
                        ->previewable()
                        ->disk('public'),
                    Components\FileUpload::make('foto_kegiatan')
                        ->label('Foto Kegiatan')
                        ->multiple()
                        ->disabled()
                        ->previewable()
                        ->disk('public'),
                ])
                ->columns(2),

            // Komentar Admin (jika ada)
            Components\Section::make('Komentar Admin')
                ->schema([
                    Components\Textarea::make('komentar')
                        ->label('Komentar')
                        ->disabled()
                        ->rows(3)
                        ->placeholder('Tidak ada komentar dari admin'),
                ])
                ->visible(fn () => !empty($this->record->komentar)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->url(RiwayatPengajuanResource::getUrl('index'))
                ->icon('heroicon-o-arrow-left')
                ->color('gray'),
        ];
    }
}