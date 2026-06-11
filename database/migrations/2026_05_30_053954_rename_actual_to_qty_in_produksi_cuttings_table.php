<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->renameColumn('actual', 'qty');
        });
    }

    public function down(): void
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->renameColumn('qty', 'actual');
        });
    }
};