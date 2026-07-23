<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveShiftFromProduksiTables extends Migration
{
    public function up()
    {
        // 🔥 HAPUS SHIFT DARI TABEL CUTTING
        if (Schema::hasTable('produksi_cuttings') && Schema::hasColumn('produksi_cuttings', 'shift')) {
            Schema::table('produksi_cuttings', function (Blueprint $table) {
                $table->dropColumn('shift');
            });
        }

        // 🔥 HAPUS SHIFT DARI TABEL CRIMPING
        if (Schema::hasTable('produksi_crimpings') && Schema::hasColumn('produksi_crimpings', 'shift')) {
            Schema::table('produksi_crimpings', function (Blueprint $table) {
                $table->dropColumn('shift');
            });
        }

        // 🔥 HAPUS SHIFT DARI TABEL LINE
        if (Schema::hasTable('produksi_line') && Schema::hasColumn('produksi_line', 'shift')) {
            Schema::table('produksi_line', function (Blueprint $table) {
                $table->dropColumn('shift');
            });
        }
    }

    public function down()
    {
        // 🔥 KEMBALIKAN SHIFT (jika rollback)
        if (Schema::hasTable('produksi_cuttings') && !Schema::hasColumn('produksi_cuttings', 'shift')) {
            Schema::table('produksi_cuttings', function (Blueprint $table) {
                $table->string('shift')->nullable()->after('leader_name');
            });
        }

        if (Schema::hasTable('produksi_crimpings') && !Schema::hasColumn('produksi_crimpings', 'shift')) {
            Schema::table('produksi_crimpings', function (Blueprint $table) {
                $table->string('shift')->nullable()->after('leader_name');
            });
        }

        if (Schema::hasTable('produksi_line') && !Schema::hasColumn('produksi_line', 'shift')) {
            Schema::table('produksi_line', function (Blueprint $table) {
                $table->string('shift')->nullable()->after('leader_name');
            });
        }
    }
}