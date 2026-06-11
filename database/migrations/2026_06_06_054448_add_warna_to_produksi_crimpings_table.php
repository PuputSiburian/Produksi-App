<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produksi_crimpings', function (Blueprint $table) {
            $table->string('warna')->nullable()->after('lot_produk');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_crimpings', function (Blueprint $table) {
            $table->dropColumn('warna');
        });
    }
};