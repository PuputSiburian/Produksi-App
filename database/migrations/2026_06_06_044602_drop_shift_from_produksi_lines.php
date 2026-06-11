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
        // Cek apakah kolom shift ada, jika ada maka hapus
        if (Schema::hasColumn('produksi_line', 'shift')) {
            Schema::table('produksi_line', function (Blueprint $table) {
                $table->dropColumn('shift');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom shift jika rollback
        Schema::table('produksi_line', function (Blueprint $table) {
            $table->string('shift')->nullable();
        });
    }
};