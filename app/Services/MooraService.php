<?php

namespace App\Services;

use App\Models\RiwayatPengajuan;

class MooraService
{
    private $bobot = [
        'tingkat_kompetisi' => [
            'Internasional' => 5,
            'Nasional' => 4,
            'Wilayah' => 3,
            'Provinsi' => 2,
            'Lokal' => 1,
        ],
        'juara' => [
            'Juara 1' => 3,
            'Juara 2' => 2,
            'Juara 3' => 1,
        ],
        'jumlah_peserta' => [
            '6 Jurusan' => 1,
            '10 Perguruan Tinggi' => 2,
            '1-2 Provinsi' => 3,
            '3-4 Provinsi' => 4,
            '5 Provinsi' => 5,
            '1-2 Negara' => 6,
            '3 Negara' => 7,
        ],
    ];

    private $nominalApresiasi = [
        'Internasional' => [
            'Juara 1' => 3500000,
            'Juara 2' => 3000000,
            'Juara 3' => 2500000,
        ],
        'Nasional' => [
            'Juara 1' => 3000000,
            'Juara 2' => 2500000,
            'Juara 3' => 2000000,
        ],
        'Wilayah' => [
            'Juara 1' => 2500000,
            'Juara 2' => 2000000,
            'Juara 3' => 1500000,
        ],
        'Provinsi' => [
            'Juara 1' => 2000000,
            'Juara 2' => 1500000,
            'Juara 3' => 1000000,
        ],
        'Lokal' => [
            'Juara 1' => 1500000,
            'Juara 2' => 1000000,
            'Juara 3' => 500000,
        ],
    ];

    private function normalizeJuara(string $juara): string
    {
        $map = [
            '1st winner' => 'Juara 1',
            'first winner' => 'Juara 1',
            '1° winner' => 'Juara 1',
            'juara i' => 'Juara 1',
            'juara 1' => 'Juara 1',
            'JUARA 1' => 'Juara 1',

            '2nd winner' => 'Juara 2',
            'second winner' => 'Juara 2',
            '2° winner' => 'Juara 2',
            'juara ii' => 'Juara 2',
            'juara 2' => 'Juara 2',
            'JUARA 2' => 'Juara 2',

            '3rd winner' => 'Juara 3',
            'third winner' => 'Juara 3',
            '3° winner' => 'Juara 3',
            'juara iii' => 'Juara 3',
            'juara 3' => 'Juara 3',
            'JUARA 3' => 'Juara 3',
        ];

        $juara = strtolower(trim($juara));
        $juara = preg_replace('/\s+/', ' ', $juara);

        return $map[$juara] ?? ucfirst($juara);
    }

    private function getNominalApresiasi(string $tingkat, string $juara): int
    {
        $tingkat = ucfirst(strtolower(trim($tingkat)));
        $juara = $this->normalizeJuara($juara);

        return $this->nominalApresiasi[$tingkat][$juara] ?? 0;
    }

    /**
     * Mendapatkan data mentah sebelum dihitung MOORA
     */
    public function getRawData()
    {
        $data = RiwayatPengajuan::all();
        
        $processedData = [];
        foreach ($data as $d) {
            $tingkat = ucfirst(strtolower(trim($d->tingkat_kompetisi)));
            $juara = $this->normalizeJuara($d->juara);
            $peserta = $this->bobot['jumlah_peserta'][$d->jumlah_peserta] ?? 0;

            $tingkatBobot = $this->bobot['tingkat_kompetisi'][$tingkat] ?? 0;
            $juaraBobot = $this->bobot['juara'][$juara] ?? 0;

            $nominal = $this->getNominalApresiasi($tingkat, $juara);

            $processedData[] = [
                'id' => $d->id,
                'nama' => $d->nama,
                'nama_lomba' => $d->nama_lomba,
                'tingkat_kompetisi' => $tingkat,
                'juara' => $juara,
                'jumlah_peserta' => $d->jumlah_peserta,
                'score_components' => [
                    'tingkat_kompetisi' => $tingkatBobot,
                    'juara' => $juaraBobot,
                    'jumlah_peserta' => $peserta,
                ],
                'nominal_apresiasi' => $nominal,
                'created_at' => $d->created_at,
            ];
        }

        return $processedData;
    }

    /**
     * Menghitung MOORA dengan akumulasi prestasi per mahasiswa
     */
    public function calculateMoora()
    {
        $data = RiwayatPengajuan::all();

        // Jika tidak ada data, return array kosong
        if ($data->isEmpty()) {
            return [];
        }

        // Kelompokkan data berdasarkan nama mahasiswa
        $groupedData = [];
        foreach ($data as $d) {
            $nama = trim($d->nama);
            
            $tingkat = ucfirst(strtolower(trim($d->tingkat_kompetisi)));
            $juara = $this->normalizeJuara($d->juara);
            $peserta = $this->bobot['jumlah_peserta'][$d->jumlah_peserta] ?? 0;

            $tingkatBobot = $this->bobot['tingkat_kompetisi'][$tingkat] ?? 0;
            $juaraBobot = $this->bobot['juara'][$juara] ?? 0;
            $nominal = $this->getNominalApresiasi($tingkat, $juara);

            if (!isset($groupedData[$nama])) {
                $groupedData[$nama] = [
                    'ids' => [],
                    'nama' => $nama,
                    'prestasi' => [],
                    'total_score_components' => [
                        'tingkat_kompetisi' => 0,
                        'juara' => 0,
                        'jumlah_peserta' => 0,
                    ],
                    'total_nominal_apresiasi' => 0,
                    'earliest_date' => $d->created_at,
                    'jumlah_prestasi' => 0,
                ];
            }

            // Kumpulkan ID untuk update database nanti
            $groupedData[$nama]['ids'][] = $d->id;
            
            // Kumpulkan detail prestasi
            $groupedData[$nama]['prestasi'][] = [
                'nama_lomba' => $d->nama_lomba,
                'tingkat_kompetisi' => $tingkat,
                'juara' => $juara,
                'jumlah_peserta' => $d->jumlah_peserta,
                'nominal_apresiasi' => $nominal,
            ];

            // Akumulasi skor komponen
            $groupedData[$nama]['total_score_components']['tingkat_kompetisi'] += $tingkatBobot;
            $groupedData[$nama]['total_score_components']['juara'] += $juaraBobot;
            $groupedData[$nama]['total_score_components']['jumlah_peserta'] += $peserta;
            
            // Akumulasi nominal apresiasi
            $groupedData[$nama]['total_nominal_apresiasi'] += $nominal;
            
            // Ambil tanggal pengajuan paling awal
            if ($d->created_at < $groupedData[$nama]['earliest_date']) {
                $groupedData[$nama]['earliest_date'] = $d->created_at;
            }
            
            $groupedData[$nama]['jumlah_prestasi']++;
        }

        // Konversi ke array matrix untuk perhitungan MOORA
        $matrix = [];
        foreach ($groupedData as $studentData) {
            $matrix[] = [
                'ids' => $studentData['ids'],
                'nama' => $studentData['nama'],
                'prestasi' => $studentData['prestasi'],
                'jumlah_prestasi' => $studentData['jumlah_prestasi'],
                'score_components' => $studentData['total_score_components'],
                'total_nominal_apresiasi' => $studentData['total_nominal_apresiasi'],
                'created_at' => $studentData['earliest_date'],
            ];
        }

        // Hitung akar kuadrat dari total kuadrat tiap kriteria
        $squaredSums = [
            'tingkat_kompetisi' => 0,
            'juara' => 0,
            'jumlah_peserta' => 0,
        ];

        foreach ($matrix as $row) {
            foreach ($row['score_components'] as $key => $value) {
                $squaredSums[$key] += pow($value, 2);
            }
        }

        // Hindari pembagian dengan nol
        foreach ($squaredSums as $key => $value) {
            if ($value == 0) {
                $squaredSums[$key] = 1;
            }
        }

        // Hitung skor MOORA
        foreach ($matrix as &$row) {
            $row['score'] = (
                $row['score_components']['tingkat_kompetisi'] / sqrt($squaredSums['tingkat_kompetisi']) +
                $row['score_components']['juara'] / sqrt($squaredSums['juara']) +
                $row['score_components']['jumlah_peserta'] / sqrt($squaredSums['jumlah_peserta'])
            );
        }
        unset($row);

        // Urutkan berdasarkan skor (descending), lalu tanggal (ascending), lalu nama (ascending)
        usort($matrix, function ($a, $b) {
            // Bandingkan skor dengan presisi yang lebih ketat
            $scoreDiff = $b['score'] - $a['score'];
            if (abs($scoreDiff) > 0.0001) { // Toleransi untuk floating point
                return $scoreDiff > 0 ? 1 : -1;
            }
            
            // Jika skor sama, bandingkan tanggal (yang lebih dulu mendapat prioritas)
            $dateDiff = $a['created_at'] <=> $b['created_at'];
            if ($dateDiff !== 0) {
                return $dateDiff;
            }
            
            // Jika tanggal juga sama, bandingkan nama
            return $a['nama'] <=> $b['nama'];
        });

        // Assign peringkat berurutan (1, 2, 3, 4, 5, dst)
        foreach ($matrix as $i => &$row) {
            $row['peringkat'] = $i + 1;
        }
        unset($row);

        // Update ke database - update semua record yang terkait dengan mahasiswa tersebut
        foreach ($matrix as $row) {
            foreach ($row['ids'] as $id) {
                RiwayatPengajuan::where('id', $id)->update([
                    'score_moora' => $row['score'],
                    'peringkat' => $row['peringkat'],
                    'nominal_apresiasi' => $row['total_nominal_apresiasi'], // Total akumulasi
                ]);
            }
        }

        return array_values($matrix);
    }

    /**
     * Mendapatkan statistik data
     */
    public function getStatistics()
    {
        $data = RiwayatPengajuan::all();
        
        $stats = [
            'total_data' => $data->count(),
            'total_mahasiswa' => $data->groupBy('nama')->count(),
            'tingkat_kompetisi' => $data->groupBy('tingkat_kompetisi')->map->count(),
            'juara' => $data->groupBy('juara')->map->count(),
            'jumlah_peserta' => $data->groupBy('jumlah_peserta')->map->count(),
            'total_nominal' => $data->sum('nominal_apresiasi'),
        ];

        return $stats;
    }

    /**
     * Mendapatkan data dengan peringkat yang sudah dihitung (per mahasiswa)
     */
    public function getRankedData()
    {
        // Ambil data unik berdasarkan nama mahasiswa dengan peringkat terkecil
        $rankedData = RiwayatPengajuan::selectRaw('
                nama,
                MIN(peringkat) as peringkat,
                MAX(score_moora) as score_moora,
                SUM(nominal_apresiasi) as total_nominal_apresiasi,
                COUNT(*) as jumlah_prestasi,
                MIN(created_at) as earliest_date
            ')
            ->groupBy('nama')
            ->orderBy('peringkat', 'asc')
            ->get();

        // Ambil detail prestasi untuk setiap mahasiswa
        foreach ($rankedData as &$student) {
            $prestasi = RiwayatPengajuan::where('nama', $student->nama)
                ->select('nama_lomba', 'tingkat_kompetisi', 'juara', 'jumlah_peserta', 'created_at')
                ->orderBy('created_at', 'asc')
                ->get()
                ->toArray();
            
            $student->prestasi = $prestasi;
        }

        return $rankedData;
    }

    /**
     * Mendapatkan detail prestasi mahasiswa berdasarkan nama
     */
    public function getStudentAchievements($nama)
    {
        return RiwayatPengajuan::where('nama', $nama)
            ->orderBy('created_at', 'asc')
            ->get();
    }
}