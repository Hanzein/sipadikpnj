<x-filament-panels::page>
    <div class="space-y-6">
        {{ $this->statisticsInfolist }}
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Grafik Tingkat Kompetisi</h3>
                <div class="space-y-2">
                    @php
                        $mooraService = new App\Services\MooraService();
                        $statistics = $mooraService->getStatistics();
                        $total = $statistics['total_data'];
                    @endphp
                    
                    @foreach($statistics['tingkat_kompetisi'] as $tingkat => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm">{{ $tingkat }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $total > 0 ? ($count / $total) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Grafik Juara</h3>
                <div class="space-y-2">
                    @foreach($statistics['juara'] as $juara => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm">{{ $juara }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $total > 0 ? ($count / $total) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Distribusi Jumlah Peserta</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($statistics['jumlah_peserta'] as $peserta => $count)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $count }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $peserta }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            {{ $total > 0 ? number_format(($count / $total) * 100, 1) : 0 }}%
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>