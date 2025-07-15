<?php

namespace App\Exports;

use App\Models\HasilMoora;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class HasilMooraExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return HasilMoora::all([
            'nama',
            'nim',
            'no_surat',
            'juara',
            'nama_lomba',
            'tingkat_kompetisi',
            'score_moora',
            'nominal_apresiasi',
            'peringkat',
        ]);
    }
    public function headings(): array
    {
        return [
            'Nama Mahasiswa',
            'NIM',
            'No Surat',
            'Juara',
            'Nama Lomba',
            'Tingkat Kompetisi',
            'Nilai Akhir MOORA',
            'Besaran Apresiasi',
            'Peringkat',
        ];
    }
}
