<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Http;
use App\Models\RiwayatPengajuan;
use App\Models\SuratTugas;
use App\Models\PengaturanWaktu;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class FormPengajuanUKT extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $step = 1;
    public $totalSteps = 9;

    public $data = [
        'surat_tugas' => null,
        'no_surat' => '',
        'sertifikat_lomba' => null,
        'nama' => '',
        'nama_lomba' => '',
        'juara' => '',
        'tingkat_kompetisi' => '',
        'jumlah_peserta' => '',
        'tanggal_pelaksanaan' => '',
        'periode_semester' => '',
        'tempat_pelaksanaan' => '',
        'lembaga_penyelenggara' => '',
        'link_kompetisi' => '',
        'nama_dosen' => '',
        'nip_dosen' => '',
    ];

    public $foto_kegiatan = [];
    public $foto_web_kompetisi = null;
    public $file_peserta = null;

    // Properti untuk status periode
    public $currentPeriod = null;
    public $periodStatus = [];
    public $remainingSubmissions = 0;
    public $maxSubmissions = 5;

    public function mount()
    {
        $this->loadPeriodInfo();
        $this->data['nama'] = auth()->user()->name ?? '';
    }

    /**
     * Load informasi periode dan kuota
     */
    public function loadPeriodInfo()
    {
        $this->currentPeriod = $this->getCurrentPeriod();
        $this->periodStatus = $this->getPeriodStatus();
        $this->remainingSubmissions = $this->getRemainingSubmissions();
        $this->maxSubmissions = $this->currentPeriod ? $this->currentPeriod->batas_pengajuan : 5;
    }

    /**
     * Mendapatkan periode aktif saat ini
     */

    public function getCurrentPeriod(): ?PengaturanWaktu
    {
        return PengaturanWaktu::getCurrentPeriod();
    }

    /**
     * Mendapatkan status periode
     */
    public function getPeriodStatus(): array
    {
        $currentPeriod = $this->getCurrentPeriod();
        
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

    /**
     * Mendapatkan rentang periode aktif
     */
    public function getCurrentPeriodRange(): array
    {
        $currentPeriod = $this->getCurrentPeriod();
        
        if (!$currentPeriod) {
            return $this->getCurrentSemesterRange();
        }
        
        return [$currentPeriod->tanggal_buka, $currentPeriod->tanggal_tutup];
    }

    /**
     * Fallback untuk rentang semester
     */
    public function getCurrentSemesterRange(): array
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        if ($currentMonth >= 8 || $currentMonth == 1) {
            $semesterStart = Carbon::create($currentMonth == 1 ? $currentYear - 1 : $currentYear, 8, 1);
            $semesterEnd = Carbon::create($currentYear, 1, 31, 23, 59, 59);
        } else {
            $semesterStart = Carbon::create($currentYear, 2, 1);
            $semesterEnd = Carbon::create($currentYear, 7, 31, 23, 59, 59);
        }

        return [$semesterStart, $semesterEnd];
    }

    /**
     * Method untuk mengecek apakah mahasiswa masih bisa mengajukan
     */
    public function canSubmitPengajuan(): bool
    {
        // Cek periode aktif
        $currentPeriod = $this->getCurrentPeriod();
        
        if (!$currentPeriod || !$currentPeriod->isCurrentlyActive()) {
            return false;
        }

        // Cek kuota
        [$periodStart, $periodEnd] = $this->getCurrentPeriodRange();
        $maxSubmissions = $currentPeriod ? $currentPeriod->batas_pengajuan : 5;

        $currentPeriodSubmissions = RiwayatPengajuan::where('nim', auth()->user()->nim)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->count();

        return $currentPeriodSubmissions < $maxSubmissions;
    }

    /**
     * Method untuk mendapatkan jumlah pengajuan yang tersisa
     */
    public function getRemainingSubmissions(): int
    {
        $currentPeriod = $this->getCurrentPeriod();
        $maxSubmissions = $currentPeriod ? $currentPeriod->batas_pengajuan : 5;
        
        [$periodStart, $periodEnd] = $this->getCurrentPeriodRange();

        $currentPeriodSubmissions = RiwayatPengajuan::where('nim', auth()->user()->nim)
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->count();

        return max(0, $maxSubmissions - $currentPeriodSubmissions);
    }

    /**
     * Method untuk mendapatkan info periode saat ini
     */
    public function getCurrentPeriodInfo(): string
    {
        $currentPeriod = $this->getCurrentPeriod();
        
        if (!$currentPeriod) {
            return $this->getCurrentSemesterName();
        }
        
        return $currentPeriod->nama_periode;
    }

    /**
     * Method untuk mendapatkan nama semester saat ini (fallback)
     */
    public function getCurrentSemesterName(): string
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        
        if ($currentMonth >= 8 || $currentMonth == 1) {
            $academicYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;
            return "Semester Ganjil " . $academicYear . "/" . ($academicYear + 1);
        } else {
            return "Semester Genap " . $currentYear . "/" . ($currentYear + 1);
        }
    }

    /**
     * Method untuk membuka modal dengan validasi batas pengajuan
     */
    public function openModal()
    {
        // Refresh status periode
        $this->loadPeriodInfo();
        
        if (!$this->canSubmitPengajuan()) {
            $status = $this->getPeriodStatus();
            
            if ($status['status'] === 'tidak_ada_periode') {
                Notification::make()
                    ->title('Periode Pengajuan Tidak Aktif')
                    ->body('Tidak ada periode pengajuan aktif saat ini.')
                    ->warning()
                    ->duration(5000)
                    ->send();
            } elseif ($status['status'] === 'akan_dimulai') {
                Notification::make()
                    ->title('Periode Belum Dimulai')
                    ->body('Periode pengajuan akan dimulai ' . $status['remaining_time'])
                    ->warning()
                    ->duration(5000)
                    ->send();
            } elseif ($status['status'] === 'berakhir') {
                Notification::make()
                    ->title('Periode Sudah Berakhir')
                    ->body('Periode pengajuan sudah berakhir. Tunggu periode berikutnya.')
                    ->warning()
                    ->duration(5000)
                    ->send();
            } else {
                Notification::make()
                    ->title('Kuota Pengajuan Habis')
                    ->body('Anda sudah mencapai batas maksimal ' . $this->maxSubmissions . ' pengajuan untuk periode ini.')
                    ->warning()
                    ->duration(5000)
                    ->send();
            }
            
            return;
        }

        $this->showModal = true;
    }

    /**
     * Method untuk menutup modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function detectNoSurat()
    {
        $this->validate([
            'data.surat_tugas' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $response = Http::timeout(30)->attach(
                'image',
                file_get_contents($this->data['surat_tugas']->getRealPath()),
                $this->data['surat_tugas']->getClientOriginalName()
            )->post(env('DETECT_ST_URL_API') . '');

            if ($response->successful()) {
                $result = $response->json();
                $detectedNoSurat = $result['nomor_surat'] ?? '';
                $this->data['no_surat'] = $detectedNoSurat;

                if (SuratTugas::where('no_surat', $detectedNoSurat)->exists()) {
                    Notification::make()
                        ->title('Surat Tugas Valid')
                        ->body('Nomor surat berhasil dideteksi dan tervalidasi.')
                        ->success()
                        ->duration(3000)
                        ->send();
                    $this->nextStep();
                } else {
                    Notification::make()
                        ->title('Surat Tugas Tidak Valid')
                        ->body('Nomor surat tidak ditemukan dalam database.')
                        ->danger()
                        ->duration(5000)
                        ->send();
                }
            } else {
                Notification::make()
                    ->title('Gagal Mendeteksi')
                    ->body('Gagal mendeteksi nomor surat dari gambar.')
                    ->danger()
                    ->duration(5000)
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Terjadi kesalahan saat mengirim data ke AI: ' . $e->getMessage())
                ->danger()
                ->duration(5000)
                ->send();
        }
    }

    public function detectSertifikatLomba()
    {
        $this->validate([
            'data.sertifikat_lomba' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            $response = Http::attach(
                'image',
                file_get_contents($this->data['sertifikat_lomba']->getRealPath()),
                $this->data['sertifikat_lomba']->getClientOriginalName()
            )->post(env('DETECT_PROCESS_URL_API') . '');

            if ($response->successful()) {
                $result = $response->json();
                $this->data['juara'] = $result['juara'] ?? '';
                $this->data['nama_lomba'] = $result['nama_lomba'] ?? '';
                $this->data['nama'] = $result['nama'] ?? '';

                Notification::make()
                    ->title('Sertifikat Berhasil Diproses')
                    ->body('Data dari sertifikat berhasil diextract.')
                    ->success()
                    ->duration(3000)
                    ->send();
                $this->nextStep();
            } else {
                Notification::make()
                    ->title('Gagal Memproses')
                    ->body('Gagal mendeteksi data dari sertifikat.')
                    ->danger()
                    ->duration(5000)
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi Kesalahan')
                ->body('Terjadi kesalahan saat mengirim data ke AI: ' . $e->getMessage())
                ->danger()
                ->duration(5000)
                ->send();
        }
    }

    public function updatedDataJumlahPeserta($value)
    {
        $map = [
            '6 Jurusan' => 'Lokal',
            '10 Perguruan Tinggi' => 'Provinsi',
            '1-2 Provinsi' => 'Provinsi',
            '3-4 Provinsi' => 'Wilayah',
            '5 Provinsi' => 'Nasional',
            '1-2 Negara' => 'Nasional',
            '3 Negara' => 'Internasional',
        ];
        $this->data['tingkat_kompetisi'] = $map[$value] ?? '';
    }

    public function updatedDataTanggalPelaksanaan($value)
    {
        if ($value) {
            $this->data['periode_semester'] = $this->determinePeriodeSemester($value);
        }
    }

    public function updatePeriodeSemester()
    {
        if ($this->data['tanggal_pelaksanaan']) {
            $this->data['periode_semester'] = $this->determinePeriodeSemester($this->data['tanggal_pelaksanaan']);
        }
    }

    private function determinePeriodeSemester($tanggal)
    {
        $date = Carbon::parse($tanggal);
        $year = $date->year;
        
        // Tahun Akademik 2024/2025
        if ($year == 2024) {
            if ($date->between(Carbon::create(2024, 9, 2), Carbon::create(2025, 1, 13))) {
                return '2024/2025 Ganjil';
            } elseif ($date->lt(Carbon::create(2024, 9, 2))) {
                return '2023/2024 Genap';
            }
        } elseif ($year == 2025) {
            if ($date->between(Carbon::create(2025, 2, 10), Carbon::create(2025, 6, 18))) {
                return '2024/2025 Genap';
            } elseif ($date->gte(Carbon::create(2025, 8, 18))) {
                return '2025/2026 Ganjil';
            } elseif ($date->between(Carbon::create(2025, 1, 14), Carbon::create(2025, 2, 9))) {
                return '2024/2025 Ganjil';
            } elseif ($date->between(Carbon::create(2025, 6, 19), Carbon::create(2025, 8, 17))) {
                return '2024/2025 Genap';
            }
        } elseif ($year >= 2026) {
            $academicYear = $this->getAcademicYear($date);
            
            if ($date->month >= 8 || $date->month <= 1) {
                return $academicYear . ' Ganjil';
            } else {
                return $academicYear . ' Genap';
            }
        }
        
        return '';
    }

    private function getAcademicYear($date)
    {
        $year = $date->year;
        
        if ($date->month >= 8) {
            return $year . '/' . ($year + 1);
        } else {
            return ($year - 1) . '/' . $year;
        }
    }

    public function getMinimumDate()
    {
        return Carbon::now()->subMonths(6)->format('Y-m-d');
    }

    public function getMaximumDate()
    {
        return Carbon::now()->format('Y-m-d');
    }

    public function getAvailablePeriods()
    {
        $periods = [];
        $currentDate = Carbon::now();
        
        for ($i = 0; $i < 4; $i++) {
            $date = $currentDate->copy()->subMonths($i * 6);
            $period = $this->determinePeriodeSemester($date->format('Y-m-d'));
            
            if ($period && !in_array($period, $periods)) {
                $periods[] = $period;
            }
        }
        
        return array_unique($periods);
    }

    public function nextStep()
    {
        $rules = match ($this->step) {
            1 => ['data.surat_tugas' => 'required|file|mimes:jpg,jpeg,png|max:2048'],
            2 => ['data.no_surat' => ['required', 'string', 'regex:/^\d{1,4}\/[A-Z0-9]+\/[A-Z]{2}(?:\.\d{2}){1,3}\/\d{4}$/']],
            3 => ['data.sertifikat_lomba' => 'required|file|mimes:jpg,jpeg,png|max:2048'],
            4 => [
                'data.nama_lomba' => 'required|string',
                'data.nama' => 'required|string',
                'data.juara' => 'required|string',
            ],
            5 => [
                'data.jumlah_peserta' => 'required|string',
                'data.tingkat_kompetisi' => 'required|string',
                'data.tanggal_pelaksanaan' => [
                    'required',
                    'date',
                    'after_or_equal:' . $this->getMinimumDate(),
                    'before_or_equal:' . $this->getMaximumDate()
                ],
                'data.periode_semester' => 'required|string',
            ],
            6 => [
                'data.tempat_pelaksanaan' => 'required|string',
                'data.lembaga_penyelenggara' => 'required|string',
                'data.link_kompetisi' => 'required|url',
                'foto_web_kompetisi' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            ],
            7 => ['file_peserta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'],
            8 => [
                'data.nama_dosen' => 'required|string',
                'data.nip_dosen' => 'required|string',
            ],
            9 => [
                'foto_kegiatan' => 'nullable|array',
                'foto_kegiatan.*' => 'image|max:2048',
            ],
            default => [],
        };

        $messages = [
            'data.tanggal_pelaksanaan.after_or_equal' => 'Tanggal pelaksanaan tidak boleh lebih dari 6 bulan yang lalu.',
            'data.tanggal_pelaksanaan.before_or_equal' => 'Tanggal pelaksanaan tidak boleh lebih dari hari ini.',
            'data.periode_semester.required' => 'Periode semester harus dipilih atau terisi otomatis dari tanggal pelaksanaan.',
        ];

        try {
            $this->validate($rules, $messages);
            $this->step++;
        } catch (\Illuminate\Validation\ValidationException $e) {
            Notification::make()
                ->title('Data Tidak Lengkap')
                ->body('Mohon lengkapi data terlebih dahulu sebelum melanjutkan.')
                ->warning()
                ->duration(5000)
                ->send();
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) $this->step--;
    }

    public function submit()
    {
        // Validasi batas pengajuan sebelum submit
        if (!$this->canSubmitPengajuan()) {
            $this->loadPeriodInfo();
            Notification::make()
                ->title('Tidak Dapat Mengajukan')
                ->body('Periode pengajuan tidak aktif atau kuota sudah habis.')
                ->danger()
                ->duration(5000)
                ->send();
            return;
        }

        $this->validate([
            'data.surat_tugas' => 'required|file',
            'data.sertifikat_lomba' => 'required|file',
            'data.periode_semester' => 'required|string',
            'data.tempat_pelaksanaan' => 'required|string',
            'data.nama_dosen' => 'required|string',
            'data.nip_dosen' => 'required|string',
            'data.tanggal_pelaksanaan' => [
                'required',
                'date',
                'after_or_equal:' . $this->getMinimumDate(),
                'before_or_equal:' . $this->getMaximumDate()
            ],
            'foto_web_kompetisi' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'file_peserta' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'foto_kegiatan' => 'nullable|array',
            'foto_kegiatan.*' => 'image|max:2048',
        ], [
            'data.tanggal_pelaksanaan.after_or_equal' => 'Tanggal pelaksanaan tidak boleh lebih dari 6 bulan yang lalu.',
            'data.tanggal_pelaksanaan.before_or_equal' => 'Tanggal pelaksanaan tidak boleh lebih dari hari ini.',
        ]);

        try {
            // Pastikan field string tidak terkontaminasi file upload
            $tempatPelaksanaan = is_string($this->data['tempat_pelaksanaan']) ? $this->data['tempat_pelaksanaan'] : '';
            $namaDosen = is_string($this->data['nama_dosen']) ? $this->data['nama_dosen'] : '';
            $nipDosen = is_string($this->data['nip_dosen']) ? $this->data['nip_dosen'] : '';
            $lembagaPenyelenggara = is_string($this->data['lembaga_penyelenggara']) ? $this->data['lembaga_penyelenggara'] : '';

            $pathSurat = $this->data['surat_tugas']->store('surat', 'public');
            $pathSertifikat = $this->data['sertifikat_lomba']->store('sertifikat_lomba', 'public');
            $pathFilePeserta = $this->file_peserta?->store('peserta', 'public');
            $pathFotoWeb = $this->foto_web_kompetisi?->store('foto_web', 'public');
            $fotos = collect($this->foto_kegiatan)->map(fn($f) => $f->store('foto', 'public'))->toArray();

            RiwayatPengajuan::create([
                'nim' => auth()->user()->nim,
                'surat_tugas' => $pathSurat,
                'no_surat' => $this->data['no_surat'],
                'sertifikat_lomba' => $pathSertifikat,
                'nama' => $this->data['nama'],
                'juara' => $this->data['juara'],
                'nama_lomba' => $this->data['nama_lomba'],
                'jumlah_peserta' => $this->data['jumlah_peserta'],
                'tingkat_kompetisi' => $this->data['tingkat_kompetisi'],
                'tanggal_pelaksanaan' => $this->data['tanggal_pelaksanaan'],
                'periode_semester' => $this->data['periode_semester'],
                'tempat_pelaksanaan' => $tempatPelaksanaan,
                'lembaga_penyelenggara' => $lembagaPenyelenggara,
                'link_kompetisi' => $this->data['link_kompetisi'],
                'foto_web_kompetisi' => $pathFotoWeb,
                'file_peserta' => $pathFilePeserta,
                'nama_dosen' => $namaDosen,
                'nip_dosen' => $nipDosen,
                'foto_kegiatan' => $fotos,
                'status' => 'submitted',
            ]);

            // Reset form
            $this->resetForm();

            Notification::make()
                ->title('Pengajuan Berhasil!')
                ->body('Pengajuan bantuan UKT Anda telah berhasil dikirim dan akan diproses oleh admin.')
                ->success()
                ->duration(5000)
                ->send();

            // Dispatch event untuk refresh halaman
            $this->dispatch('pengajuan-created');
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Terjadi Kesalahan!')
                ->body('Gagal mengirim pengajuan: ' . $e->getMessage())
                ->danger()
                ->duration(5000)
                ->send();
        }
    }

    private function resetForm()
    {
        $this->data = [
            'surat_tugas' => null,
            'no_surat' => '',
            'sertifikat_lomba' => null,
            'nama' => auth()->user()->name ?? '',
            'nama_lomba' => '',
            'juara' => '',
            'tingkat_kompetisi' => '',
            'jumlah_peserta' => '',
            'tanggal_pelaksanaan' => '',
            'periode_semester' => '',
            'tempat_pelaksanaan' => '',
            'lembaga_penyelenggara' => '',
            'link_kompetisi' => '',
            'nama_dosen' => '',
            'nip_dosen' => '',
        ];

        $this->foto_kegiatan = [];
        $this->foto_web_kompetisi = null;
        $this->file_peserta = null;
        $this->showModal = false;
        $this->step = 1;
    }

    public function render()
    {
        // Refresh data sebelum render
        $this->loadPeriodInfo();
        
        return view('livewire.form-pengajuan-u-k-t', [
            'canSubmit' => $this->canSubmitPengajuan(),
            'remainingSubmissions' => $this->remainingSubmissions,
            'maxSubmissions' => $this->maxSubmissions,
            'periodStatus' => $this->periodStatus,
            'currentPeriodInfo' => $this->getCurrentPeriodInfo(),
        ]);
    }
}