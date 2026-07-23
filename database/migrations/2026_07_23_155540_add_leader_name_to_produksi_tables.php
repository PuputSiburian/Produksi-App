<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLeaderNameToProduksiTables extends Migration
{
    public function up()
    {
        // ============================================
        // TABEL CUTTING (produksi_cuttings)
        // ============================================
        if (Schema::hasTable('produksi_cuttings')) {
            if (!Schema::hasColumn('produksi_cuttings', 'leader_name')) {
                Schema::table('produksi_cuttings', function (Blueprint $table) {
                    $table->string('leader_name')->nullable()->after('user_id');
                });
            }
            if (!Schema::hasColumn('produksi_cuttings', 'shift')) {
                Schema::table('produksi_cuttings', function (Blueprint $table) {
                    $table->string('shift')->nullable()->after('leader_name');
                });
            }
        }

        // ============================================
        // TABEL CRIMPING (produksi_crimpings)
        // ============================================
        if (Schema::hasTable('produksi_crimpings')) {
            if (!Schema::hasColumn('produksi_crimpings', 'leader_name')) {
                Schema::table('produksi_crimpings', function (Blueprint $table) {
                    $table->string('leader_name')->nullable()->after('user_id');
                });
            }
            if (!Schema::hasColumn('produksi_crimpings', 'shift')) {
                Schema::table('produksi_crimpings', function (Blueprint $table) {
                    $table->string('shift')->nullable()->after('leader_name');
                });
            }
        }

        // ============================================
        // 🔥 TABEL LINE (produksi_line - TANPA 's')
        // ============================================
        if (Schema::hasTable('produksi_line')) {
            if (!Schema::hasColumn('produksi_line', 'leader_name')) {
                Schema::table('produksi_line', function (Blueprint $table) {
                    $table->string('leader_name')->nullable()->after('user_id');
                });
            }
            if (!Schema::hasColumn('produksi_line', 'shift')) {
                Schema::table('produksi_line', function (Blueprint $table) {
                    $table->string('shift')->nullable()->after('leader_name');
                });
            }
        }
    }

    public function down()
    {
        // TABEL CUTTING
        if (Schema::hasTable('produksi_cuttings') && Schema::hasColumn('produksi_cuttings', 'leader_name')) {
            Schema::table('produksi_cuttings', function (Blueprint $table) {
                $table->dropColumn(['leader_name', 'shift']);
            });
        }

        // TABEL CRIMPING
        if (Schema::hasTable('produksi_crimpings') && Schema::hasColumn('produksi_crimpings', 'leader_name')) {
            Schema::table('produksi_crimpings', function (Blueprint $table) {
                $table->dropColumn(['leader_name', 'shift']);
            });
        }

        // 🔥 TABEL LINE (produksi_line - TANPA 's')
        if (Schema::hasTable('produksi_line') && Schema::hasColumn('produksi_line', 'leader_name')) {
            Schema::table('produksi_line', function (Blueprint $table) {
                $table->dropColumn(['leader_name', 'shift']);
            });
        }
    }
}