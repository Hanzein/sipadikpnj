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
        Schema::create('kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kriteria'); // Nama Kriteria (Tingkat Kejuaraan, Peringkat, Sertifikat)
            $table->decimal('bobot', 5, 2); // Bobot Kriteria (0.4, 0.35, 0.25)
            $table->enum('jenis', ['benefit', 'cost']); // Benefit (Semakin besar semakin baik) atau Cost
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria');
    }
};
