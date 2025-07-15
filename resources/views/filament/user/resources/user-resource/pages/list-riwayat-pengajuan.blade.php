<x-filament::page>
    {{-- Tombol dan form modal --}}
    @livewire('form-pengajuan-ukt')
    
    {{-- Tabel bawaan dari Filament --}}
    <div wire:key="table-{{ now() }}">
        {{ $this->table }}
    </div>
    
    @script
    <script>
        // Refresh tabel ketika ada event refresh-table
        Livewire.on('refresh-table', () => {
            // Refresh komponen tabel
            window.location.reload();
        });
    </script>
    @endscript
</x-filament::page>