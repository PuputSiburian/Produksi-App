<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');           // Nama tabel yang diubah
            $table->unsignedBigInteger('record_id'); // ID record yang diubah
            $table->string('action');                // UPDATE, DELETE
            $table->json('old_data')->nullable();    // Data sebelum diubah
            $table->json('new_data')->nullable();    // Data setelah diubah
            $table->unsignedBigInteger('user_id');   // User yang melakukan perubahan
            $table->string('user_name');             // Nama user
            $table->string('ip_address')->nullable(); // IP address
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};