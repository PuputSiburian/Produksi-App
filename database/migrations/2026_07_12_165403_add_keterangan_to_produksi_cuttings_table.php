<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeteranganToProduksiCuttingsTable extends Migration
{
    public function up()
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->after('reject');
        });
    }

    public function down()
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
}