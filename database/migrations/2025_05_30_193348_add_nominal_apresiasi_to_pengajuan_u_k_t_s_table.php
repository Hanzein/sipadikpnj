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
            $table->unsignedBigInteger('nominal_apresiasi')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_u_k_t_s', function (Blueprint $table) {
            $table->dropColumn(['nominal_apresiasi']);
        });
    }
};
