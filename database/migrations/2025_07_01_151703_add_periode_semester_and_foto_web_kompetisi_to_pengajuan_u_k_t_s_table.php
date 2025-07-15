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
        Schema::table('pengajuan_u_k_t_s', function (Blueprint $table) {
            $table->string('periode_semester')->nullable();
            $table->string('foto_web_kompetisi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_u_k_t_s', function (Blueprint $table) {
            $table->dropColumn(['periode_semester', 'foto_web_kompetisi']);
        });
    }
};
