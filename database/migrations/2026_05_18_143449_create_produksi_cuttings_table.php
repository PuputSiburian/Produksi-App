<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_cuttings', function (Blueprint $table) {

            $table->id();
            $table->date('tanggal');
            $table->string('line_cutting');
            $table->string('produk');
            $table->string('part_number');
            $table->integer('target');
            $table->integer('actual');
            $table->integer('reject')->default(0);

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_cuttings');
    }
};