<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesins', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mesin')->unique();
            $table->string('nama_mesin');
            $table->string('jenis_mesin')->nullable();
            $table->string('lokasi')->nullable();
            $table->enum('status', ['Beroperasi', 'Perbaikan', 'Rusak', 'Maintenance', 'Idle'])->default('Idle');
            $table->text('gangguan')->nullable();
            $table->date('tanggal_gangguan')->nullable();
            $table->string('teknisi')->nullable();
            $table->integer('durasi_gangguan')->nullable()->comment('Durasi dalam jam');
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi', 'Darurat'])->default('Sedang');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesins');
    }
};