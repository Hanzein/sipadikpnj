<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturan_waktu', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->datetime('tanggal_buka');
            $table->datetime('tanggal_tutup');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->string('tahun_akademik'); // contoh: 2024/2025
            $table->boolean('is_active')->default(false);
            $table->text('deskripsi')->nullable();
            $table->integer('batas_pengajuan')->default(5); // batas maksimal pengajuan per mahasiswa
            $table->timestamps();

            // Index untuk performa query
            $table->index(['is_active', 'tanggal_buka', 'tanggal_tutup']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_waktu');
    }
};