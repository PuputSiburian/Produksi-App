<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            // Tambah kolom proses setelah nama_operator
            $table->string('proses')->nullable()->after('nama_operator');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->dropColumn('proses');
        });
    }
};