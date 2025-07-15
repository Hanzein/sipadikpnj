<x-filament::card>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="text-center">
            <p class="text-sm text-gray-500">Program Studi</p>
            <p class="text-xl font-semibold">{{ $user->prodi ?? '-' }}</p>
        </div>

        <div class="text-center">
            <p class="text-sm text-gray-500">Jurusan</p>
            <p class="text-xl font-semibold">{{ $user->jurusan ?? '-' }}</p>
        </div>
    </div>
</x-filament::card>
