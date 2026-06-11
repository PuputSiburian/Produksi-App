<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus dulu foreign key jika ada
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->dropColumn('part_number');
        });
        
        // Tambahkan lagi sebagai nullable
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->string('part_number')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->dropColumn('part_number');
        });
    }
};