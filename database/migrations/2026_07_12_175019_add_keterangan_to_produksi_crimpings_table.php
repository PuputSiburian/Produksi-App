<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganToProduksiCrimpingsTable extends Migration
{
    public function up()
    {
        Schema::table('produksi_crimpings', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('reject');
        });
    }

    public function down()
    {
        Schema::table('produksi_crimpings', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
}