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
        Schema::create('pengajuan_u_k_t_s', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->foreign('nim')->references('nim')->on('users')->onDelete('cascade');
            $table->string('surat_tugas')->nullable();
            $table->string('no_surat')->nullable();
            $table->string('sertifikat_lomba')->nullable();
            $table->string('nama')->nullable();
            $table->string('juara')->nullable();
            $table->string('nama_lomba')->nullable();
            $table->string('jumlah_peserta')->nullable();
            $table->string('tingkat_kompetisi')->nullable();
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->string('tempat_pelaksanaan')->nullable();
            $table->string('lembaga_penyelenggara')->nullable();
            $table->string('link_kompetisi')->nullable();
            $table->string('nama_dosen')->nullable();
            $table->string('nip_dosen')->nullable();
            $table->string('file_peserta')->nullable();
            $table->json('foto_kegiatan')->nullable();
            $table->enum('status', ['submitted', 'rejected', 'accepted'])->default('submitted');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_u_k_t_s');
    }
};
