<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produksi_line', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('proses');
            $table->string('nama_line');
            $table->string('shift');
            $table->string('nama_operator');
            $table->string('produk');
            $table->string('part_number');
            $table->string('lot_produk');
            $table->integer('target');
            $table->integer('qty');
            $table->integer('reject')->nullable();
            $table->integer('downtime')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produksi_line');
    }
};