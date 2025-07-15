<x-filament::widget>
    <x-filament::card>
        <h2 class="text-xl font-bold mb-4">
            Statistik Pengajuan Diterima per Program Studi
        </h2>

        @if ($statistik->count())
            <ul class="space-y-2">
                @foreach ($statistik as $row)
                    <li class="flex justify-between">
                        <span>{{ $row->prodi ?? 'Tidak diketahui' }}</span>
                        <span class="font-semibold">{{ $row->total }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Belum ada pengajuan yang disetujui.</p>
        @endif
    </x-filament::card>
</x-filament::widget>
