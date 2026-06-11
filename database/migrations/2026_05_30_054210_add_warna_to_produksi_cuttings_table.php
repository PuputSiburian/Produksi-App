<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up(): void
{
    Schema::table('produksi_cuttings', function (Blueprint $table) {
        if (!Schema::hasColumn('produksi_cuttings', 'warna')) {
            $table->string('warna')->nullable()->after('qty');
        }
    });
}
    public function down(): void
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->dropColumn('warna');
        });
    }
};