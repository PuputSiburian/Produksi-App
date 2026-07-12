<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropActualFromProduksiCuttingsTable extends Migration
{
    public function up()
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->dropColumn('actual');
        });
    }

    public function down()
    {
        Schema::table('produksi_cuttings', function (Blueprint $table) {
            $table->integer('actual')->nullable();
        });
    }
}