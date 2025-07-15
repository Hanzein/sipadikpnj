<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\RiwayatPengajuanResource\Pages;
use App\Filament\User\Resources\RiwayatPengajuanResource\RelationManagers;
use App\Models\RiwayatPengajuan;
use App\Models\PengaturanWaktu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

class RiwayatPengajuanResource extends Resource
{
    protected static ?string $model = RiwayatPengajuan::class;
    protected static ?string $navigationLabel = 'Riwayat Pengajuan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Mahasiswa';
    protected static ?string $slug = 'riwayat-pengajuan';

    public static function getLabel(): string
    {
        return 'Riwayat Pengajuan';
    }

    public static function getPluralLabel(): string
    {
        return 'Riwayat Pengajuan';
    }

    // Method untuk mendapatkan periode aktif saat ini
    public static function getCurrentPeriod(): ?PengaturanWaktu
    {
        return PengaturanWaktu::getCurrentPeriod();
    }

    // Method untuk mengecek apakah periode pengajuan sedang aktif
    public static function isSubmissionPeriodActive(): bool
    {
        $currentPeriod = self::getCurrentPeriod();
        return $currentPeriod && $currentPeriod->isCurrentlyActive();
    }

    // Method untuk mendapatkan rentang waktu periode aktif
    public static function getCurrentPeriodRange(): array
    {
        $currentPeriod = self::getCurrentPeriod();
        
        if (!$currentPeriod) {
            // Fallback ke sistem semester lama jika tidak ada periode aktif
            return self::getCurrentSemesterRange();
        }
        
        return [$currentPeriod->tanggal_buka, $currentPeriod->tanggal_tutup];
    }

    // Method fallback untuk rentang semester (jika tidak ada periode aktif)
    public static function getCurrentSemesterRange(): array
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        // Tentukan semester berdasarkan bulan
        if ($currentMonth >= 8 || $currentMonth == 1) {
            // Semester Ganjil
            $semesterStart = Carbon::create($currentMonth == 1 ? $currentYear - 1 : $currentYear, 8, 1);
            $semesterEnd = Carbon::create($currentYear, 1, 31, 23, 59, 59);
        } else {
            // Semester Genap
            $semesterStart = Carbon::create($currentYear, 2, 1);
            $semesterEnd = Carbon::create($currentYear, 7, 31, 23, 59, 59);
        }

        return [$semesterStart, $semesterEnd];
    }

    public static function getEloquentQuery(): Builder
    {
        // Dapatkan rentang periode aktif
        [$periodStart, $periodEnd] = self::getCurrentPeriodRange();

        return parent::getEloquentQuery()
            ->where('nim', auth()->user()->nim) // hanya tampilkan data milik user yang sedang login
            ->whereBetween('created_at', [$periodStart, $periodEnd]); // hanya tampilkan data periode aktif
    }

    // Method untuk mengecek apakah mahasiswa masih bisa mengajukan
    public static function canSubmitPengajuan(): bool
    {
        // Cek apakah periode pengajuan aktif
        if (!self::isSubmissionPeriodActive()) {
            return false;
        }

        $currentPeriod = self::getCurrentPeriod();
        $maxSubmissions = $currentPeriod ? $currentPeriod->batas_pengajuan : 5;
        
        [$periodStart, $periodEnd] = self::getCurrentPeriodRange();

        // Hitung jumlah pengajuan di periode ini
        $currentPeriodSubmissions = RiwayatPengajuan::where('nim', auth()->user()->nim)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->count();

        return $currentPeriodSubmissions < $maxSubmissions;
    }

    // Method untuk mendapatkan jumlah pengajuan yang tersisa
    public static function getRemainingSubmissions(): int
    {
        $currentPeriod = self::getCurrentPeriod();
        $maxSubmissions = $currentPeriod ? $currentPeriod->batas_pengajuan : 5;
        
        [$periodStart, $periodEnd] = self::getCurrentPeriodRange();

        $currentPeriodSubmissions = RiwayatPengajuan::where('nim', auth()->user()->nim)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->count();

        return max(0, $maxSubmissions - $currentPeriodSubmissions);
    }

    // Method untuk mendapatkan info periode saat ini
    public static function getCurrentPeriodInfo(): string
    {
        $currentPeriod = self::getCurrentPeriod();
        
        if (!$currentPeriod) {
            return self::getCurrentSemesterName();
        }
        
        return $currentPeriod->nama_periode;
    }

    // Method untuk mendapatkan nama semester saat ini (fallback)
    public static function getCurrentSemesterName(): string
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        if ($currentMonth >= 8 || $currentMonth == 1) {
            // Semester Ganjil
            $academicYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
            return "Semester Ganjil " . $academicYear . "/" . ($academicYear + 1);
        } else {
            // Semester Genap
            return "Semester Genap " . $currentYear . "/" . ($currentYear + 1);
        }
    }

    // Method untuk mendapatkan status periode
    public static function getPeriodStatus(): array
    {
        $currentPeriod = self::getCurrentPeriod();
        
        if (!$currentPeriod) {
            return [
                'status' => 'tidak_ada_periode',
                'message' => 'Tidak ada periode pengajuan aktif',
                'color' => 'gray'
            ];
        }
        
        if (!$currentPeriod->isCurrentlyActive()) {
            if ($currentPeriod->isUpcoming()) {
                return [
                    'status' => 'akan_dimulai',
                    'message' => 'Periode belum dimulai',
                    'color' => 'warning',
                    'remaining_time' => $currentPeriod->tanggal_buka->diffForHumans()
                ];
            } else {
                return [
                    'status' => 'berakhir',
                    'message' => 'Periode sudah berakhir',
                    'color' => 'danger'
                ];
            }
        }
        
        return [
            'status' => 'aktif',
            'message' => 'Periode sedang berlangsung',
            'color' => 'success',
            'remaining_time' => $currentPeriod->getRemainingTime()
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form schema akan disesuaikan dengan kebutuhan pengajuan UKT
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('nim')
                    ->required()
                    ->disabled()
                    ->default(fn() => auth()->user()->nim),
                
                Forms\Components\TextInput::make('nama_lomba')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\Select::make('tingkat_kompetisi')
                    ->options([
                        'Lokal' => 'Lokal',
                        'Nasional' => 'Nasional',
                        'Internasional' => 'Internasional',
                    ])
                    ->required(),
                
                Forms\Components\Select::make('juara')
                    ->options([
                        'Juara 1' => 'Juara 1',
                        'Juara 2' => 'Juara 2',
                        'Juara 3' => 'Juara 3',
                        'Harapan 1' => 'Harapan 1',
                        'Harapan 2' => 'Harapan 2',
                        'Harapan 3' => 'Harapan 3',
                    ])
                    ->required(),
                
                Forms\Components\FileUpload::make('file_sertifikat')
                    ->label('Sertifikat')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->required(),
                
                Forms\Components\Textarea::make('deskripsi')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        $periodStatus = self::getPeriodStatus();
        $currentPeriod = self::getCurrentPeriod();
        $maxSubmissions = $currentPeriod ? $currentPeriod->batas_pengajuan : 5;

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama'),
                Tables\Columns\TextColumn::make('nama_lomba')->label('Nama Lomba'),
                Tables\Columns\TextColumn::make('tingkat_kompetisi')->label('Tingkat Kompetisi'),
                Tables\Columns\TextColumn::make('juara'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'submitted' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('komentar')
                    ->label('Komentar Admin')
                    ->default('Tidak ada komentar')
                    ->formatStateUsing(fn (?string $state) => $state ?? 'Tidak ada komentar')
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')->label('Dikirim')->date(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('period_info')
                    ->label(fn () => 'Periode: ' . self::getCurrentPeriodInfo())
                    ->icon('heroicon-o-information-circle')
                    ->color('info')
                    ->action(function () {
                        // Tidak ada aksi, hanya sebagai informasi
                    })
                    ->disabled(),
                
                Tables\Actions\Action::make('period_status')
                    ->label(fn () => $periodStatus['message'] . 
                        (isset($periodStatus['remaining_time']) ? ' (' . $periodStatus['remaining_time'] . ')' : ''))
                    ->icon(fn () => match($periodStatus['status']) {
                        'aktif' => 'heroicon-o-check-circle',
                        'akan_dimulai' => 'heroicon-o-clock',
                        'berakhir' => 'heroicon-o-x-circle',
                        'tidak_ada_periode' => 'heroicon-o-exclamation-triangle',
                    })
                    ->color(fn () => $periodStatus['color'])
                    ->action(function () {
                        // Tidak ada aksi, hanya sebagai informasi
                    })
                    ->disabled(),
                
                Tables\Actions\Action::make('quota_info')
                    ->label(fn () => 'Sisa Kuota: ' . self::getRemainingSubmissions() . '/' . $maxSubmissions)
                    ->icon('heroicon-o-chart-bar')
                    ->color(fn () => self::getRemainingSubmissions() > 0 ? 'success' : 'danger')
                    ->action(function () {
                        // Tidak ada aksi, hanya sebagai informasi
                    })
                    ->disabled(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ]),
                
                Tables\Filters\SelectFilter::make('tingkat_kompetisi')
                    ->options([
                        'Lokal' => 'Lokal',
                        'Nasional' => 'Nasional',
                        'Internasional' => 'Internasional',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => RiwayatPengajuanResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn ($record) => $record->status === 'rejected' && self::isSubmissionPeriodActive()),
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
            'index' => Pages\ListRiwayatPengajuan::route('/'),
            'create' => Pages\CreateRiwayatPengajuan::route('/create'),
            'edit' => Pages\EditRiwayatPengajuan::route('/{record}/edit'),
            'view' => Pages\ViewDetailPengajuan::route('/{record}'),
        ];
    }
}