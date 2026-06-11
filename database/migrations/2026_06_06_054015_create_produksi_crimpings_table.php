<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('produksi_crimpings', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('line_crimping');
            $table->string('nama_operator');
            $table->string('produk');
            $table->string('part_number');
            $table->string('lot_produk')->nullable();
            $table->string('warna')->nullable();
            $table->integer('target');
            $table->integer('qty');
            $table->integer('reject')->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produksi_crimpings');
    }
};