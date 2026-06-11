<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->string('nama_operator')->nullable()->after('line_cutting');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->dropColumn('nama_operator');
        });
    }
};