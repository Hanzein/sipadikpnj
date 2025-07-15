<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_u_k_t_s', function (Blueprint $table) {
            $table->float('score_moora')->nullable();
            $table->integer('peringkat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_u_k_t_s', function (Blueprint $table) {
           $table->dropColumn(['score_moora', 'peringkat']); 
        });
    }
};
