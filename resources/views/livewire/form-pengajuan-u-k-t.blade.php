<div>
    {{-- Informasi Batas Pengajuan --}}
    <div class="mb-4 p-4 rounded-lg border {{ $canSubmit ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold {{ $canSubmit ? 'text-green-800' : 'text-red-800' }}">
                    Status Pengajuan Semester Ini
                </h3>
                <p class="text-sm {{ $canSubmit ? 'text-green-600' : 'text-red-600' }}">
                    @if($canSubmit)
                        Sisa pengajuan: <span class="font-bold">{{ $remainingSubmissions }}</span> dari 5 pengajuan
                    @else
                        <span class="font-bold">Batas pengajuan telah tercapai (10/10)</span>
                    @endif
                </p>
            </div>
            <div class="flex items-center">
                @if($canSubmit)
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>
        </div>
    </div>

    {{-- Tombol untuk membuka popup --}}
    @if($canSubmit)
        <x-filament::button 
            wire:click="openModal" 
            class="mb-4 bg-[#008697] hover:bg-[#007280] text-white px-6 py-3 text-base">
            Pengajuan Bantuan UKT
        </x-filament::button>
    @else
        <x-filament::button 
        disabled
        class="mb-4 bg-[#2F2F2F] text-white px-6 py-3 text-base cursor-not-allowed opacity-100">
        Batas Pengajuan Tercapai
         </x-filament::button>

    @endif

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white w-full max-w-4xl rounded-xl shadow-lg overflow-y-auto max-h-screen p-6 relative">
                
                {{-- Header dengan layout flex --}}
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold mt-2 mb-2">Form Pengajuan Bantuan UKT</h2>
                        <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                            <span>Langkah {{ $step }} dari {{ $totalSteps }}</span>
                            <span>{{ round(($step / $totalSteps) * 100) }}% selesai</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
                        </div>
                    </div>
                    <button wire:click="closeModal" class="ml-4 text-gray-500 hover:text-black text-2xl font-bold">&times;</button>
                </div>

                {{-- Flash message --}}
                @if (session()->has('success'))
                    <div style="background-color: #d1fae5; color: #065f46; padding: 12px; border-radius: 0.375rem; margin-top: 1rem; margin-bottom: 1rem">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div style="background-color: #fee2e2; color: #991b1b; padding: 12px; border-radius: 0.375rem; margin-top: 1rem; margin-bottom: 1rem">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-4">
                @if ($step === 1)
                    <div>
                        <label class="block font-medium">Upload Surat Tugas</label>
                        <p class="text-sm text-gray-600 mt-3">
                            Untuk contoh surat tugas dapat dilihat di bawah ini: <br>
                            <a href="https://drive.google.com/file/d/1iNWxr1hg5HwZK3H8hecXQM9v2oDDq7Ie/view?usp=sharing" 
                            class="text-blue-600 underline" target="_blank">Klik di sini untuk melihat contoh</a>
                        </p>
                        <input type="file" wire:model="data.surat_tugas" accept=".jpg,.jpeg,.png" class="mt-3 block w-full border border-gray-300 rounded" />
                        <p class="text-sm text-gray-600 mt-3">
                            <strong>Note*:</strong><br>
                            <ul class="list-disc list-inside mt-1">
                            <li>Upload file dengan maksimal 1 gambar/halaman yang berisikan surat tugas seperti contoh di atas.</li>
                            <li>Hanya boleh upload file dengan format <code>.jpg</code>, <code>.jpeg</code>, atau <code>.png</code>.</li>
                            <li><strong>Tidak</strong> menerima file berformat <code>.pdf</code>.</li>
                            </ul>
                        </p>
                        @error('data.surat_tugas') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                @elseif ($step === 2)
                    <div>
                        <label class="block font-medium">Nomor Surat</label>
                        <input type="text" wire:model="data.no_surat" class="mt-1 block w-full border border-gray-300 rounded" />
                        @error('data.no_surat') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                @elseif ($step === 3)
                    <div>
                        <label class="block font-medium">Upload Sertifikat Lomba</label>
                        <p class="text-sm text-gray-600 mt-3">
                            Untuk contoh sertifikat lomba dapat dilihat di bawah ini: <br>
                            <a href="https://drive.google.com/file/d/1wBUB-MB-8tKdV8kJHFeCXmrM6aqu8j1o/view?usp=sharing" 
                            class="text-blue-600 underline" target="_blank">Klik di sini untuk melihat contoh</a>
                        </p>
                        <input type="file" wire:model="data.sertifikat_lomba" accept=".jpg,.jpeg,.png" class="mt-3 block w-full border border-gray-300 rounded" />
                        <p class="text-sm text-gray-600 mt-3">
                        <strong>Note*:</strong><br>
                            <ul class="list-disc list-inside mt-1">
                            <li>Upload file dengan maksimal 1 gambar/halaman yang berisikan sertifikat lomba seperti contoh di atas.</li>
                            <li>Hanya boleh upload file dengan format <code>.jpg</code>, <code>.jpeg</code>, atau <code>.png</code>.</li>
                            <li><strong>Tidak</strong> menerima file berformat <code>.pdf</code>.</li>
                            </ul>
                        </p>
                        @error('data.sertifikat_lomba') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                @elseif ($step === 4)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium">Nama</label>
                            <input type="text" wire:model="data.nama" class="mt-1 block w-full border border-gray-300 rounded" />
                            @error('data.nama') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block font-medium">Juara</label>
                            <input type="text" wire:model="data.juara" class="mt-1 block w-full border border-gray-300 rounded" />
                            @error('data.juara') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-medium">Nama Lomba</label>
                            <input type="text" wire:model="data.nama_lomba" class="mt-1 block w-full border border-gray-300 rounded" />
                            @error('data.nama_lomba') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    @elseif ($step === 5)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tingkat Kompetisi sebagai Dropdown -->
                        <div>
                            <label class="block font-medium">Tingkat Kompetisi</label>
                            <select wire:model="data.tingkat_kompetisi" class="mt-1 block w-full border border-gray-300 rounded" disabled>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="Lokal">Lokal</option>
                                <option value="Provinsi">Provinsi</option>
                                <option value="Wilayah">Wilayah</option>
                                <option value="Nasional">Nasional</option>
                                <option value="Internasional">Internasional</option>
                            </select>
                            @error('data.tingkat_kompetisi') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Jumlah Peserta sebagai Dropdown -->
                        <div>
                            <label class="block font-medium">Jumlah Peserta Kompetisi</label>
                            <select wire:model="data.jumlah_peserta" class="mt-1 block w-full border border-gray-300 rounded">
                                <option value="">-- Pilih Jumlah Peserta --</option>
                                <option value="6 Jurusan">6 Jurusan atau lebih</option>
                                <option value="10 Perguruan Tinggi">10 Perguruan Tinggi atau lebih</option>
                                <option value="1-2 Provinsi">1-2 Provinsi</option>
                                <option value="3-4 Provinsi">3-4 Provinsi</option>
                                <option value="5 Provinsi">5 Provinsi atau lebih</option>
                                <option value="1-2 Negara">1-2 Negara</option>
                                <option value="3 Negara">3 Negara atau lebih</option>
                            </select>
                            @error('data.jumlah_peserta') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <!-- Tanggal Pelaksanaan -->
                        <div>
                            <label class="block font-medium">Tanggal Pelaksanaan</label>
                            <input type="date" 
                                wire:model="data.tanggal_pelaksanaan" 
                                min="{{ $this->getMinimumDate() }}" 
                                max="{{ $this->getMaximumDate() }}"
                                class="mt-1 block w-full border border-gray-300 rounded" />
                            @error('data.tanggal_pelaksanaan') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Periode Semester -->
                        <div>
                            <label class="block font-medium">Periode Semester (Ganjil/Genap)</label>
                            <input type="text" 
                                wire:model="data.periode_semester"
                                readonly 
                                class="mt-1 block w-full border border-gray-300 rounded bg-gray-50" 
                                placeholder="Akan terisi otomatis berdasarkan tanggal pelaksanaan" />
                            @error('data.periode_semester') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                @elseif ($step === 6)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium">Tempat Pelaksanaan</label>
                            <input type="text" 
                                wire:model.defer="data.tempat_pelaksanaan" 
                                class="mt-1 block w-full border border-gray-300 rounded" 
                                placeholder="Contoh: Gor Ciracas" />
                            @error('data.tempat_pelaksanaan') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block font-medium">Lembaga Penyelenggara Kompetisi</label>
                            <input type="text" 
                                wire:model.defer="data.lembaga_penyelenggara" 
                                class="mt-1 block w-full border border-gray-300 rounded" 
                                placeholder="Contoh: KONI" />
                            @error('data.lembaga_penyelenggara') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block font-medium">Link Kompetisi</label>
                            <input type="url" 
                                wire:model.defer="data.link_kompetisi" 
                                class="mt-1 block w-full border border-gray-300 rounded" 
                                placeholder="https://contoh.com" />
                            @error('data.link_kompetisi') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block font-medium">Upload Screenshot Web Kompetisi</label>
                            <input type="file" 
                                wire:model="foto_web_kompetisi" 
                                accept=".jpg,.jpeg,.png" 
                                class="mt-1 block w-full border border-gray-300 rounded" />
                                <p class="text-sm text-gray-600 mt-3">
                                    <strong>Note*:</strong><br>
                                    <ul class="list-disc list-inside mt-1">
                                    <li>Diharapkan mengupload screenshot untuk web kompetisi/sosial media sebagai sumber informasi lomba</li>
                                    </ul>
                                </p>
                            @error('foto_web_kompetisi') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
            @elseif ($step === 7)
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium">Upload Daftar Peserta (Opsional)</label>
                        <input type="file" wire:model="file_peserta" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full border border-gray-300 rounded" />
                        @error('file_peserta') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
                @elseif ($step === 8)
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium">Nama Dosen Pembimbing/Pembina</label>
                        <input type="text" wire:model.defer="data.nama_dosen" class="mt-1 block w-full border border-gray-300 rounded" placeholder="Contoh: Rahmat Subarkah" />
                        @error('data.nama_dosen') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-medium">NIP Dosen Pembimbing/Pembina</label>
                        <input type="text" wire:model.defer="data.nip_dosen" class="mt-1 block w-full border border-gray-300 rounded" placeholder="Contoh: 198001012020121001" />
                        @error('data.nip_dosen') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
                @elseif ($step === 9)
                <div class="space-y-4">
                    <div>
                        <label class="block font-medium">Upload Foto Kegiatan (Opsional)</label>
                        <input
                            type="file"
                            multiple
                            wire:model="foto_kegiatan"
                            accept=".jpg,.jpeg,.png"
                            class="mt-1 block w-full border border-gray-300 rounded"/>
                        @error('foto_kegiatan') 
                            <span class="text-sm text-red-600">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
                @endif
            </div>

            {{-- Tombol navigasi langkah --}}
            <div class="mt-6 flex justify-between">
                @if ($step > 1)
                    <x-filament::button wire:click="previousStep" outlined>
                        Sebelumnya
                    </x-filament::button>
                @endif

                @if ($step === 1)
                <x-filament::button 
                    wire:click="detectNoSurat"
                    wire:loading.attr="disabled"
                    wire:target="detectNoSurat" >
                    <span wire:loading.remove wire:target="detectNoSurat">Deteksi</span>
                    <span wire:loading wire:target="detectNoSurat">Memproses...</span>
                </x-filament::button>
                @elseif ($step === 3)
                <x-filament::button 
                    wire:click="detectSertifikatLomba" 
                    wire:loading.attr="disabled" 
                    wire:target="detectSertifikatLomba">
                    <span wire:loading.remove wire:target="detectSertifikatLomba">Deteksi</span>
                    <span wire:loading wire:target="detectSertifikatLomba">Memproses...</span>
                </x-filament::button>
                @elseif ($step < $totalSteps)
                    <x-filament::button wire:click="nextStep">
                        Selanjutnya
                    </x-filament::button>
                @else
                    <x-filament::button wire:click="submit" color="success">
                        Kirim Pengajuan
                    </x-filament::button>
                @endif
            </div>

            </div>
        </div>
    @endif
</div>
