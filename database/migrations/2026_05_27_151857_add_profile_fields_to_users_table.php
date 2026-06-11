<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {

        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users','role')) {

                $table->string('role')
                      ->default('Admin');

            }

            if (!Schema::hasColumn('users','jabatan')) {

                $table->string('jabatan')
                      ->nullable();

            }

            if (!Schema::hasColumn('users','divisi')) {

                $table->string('divisi')
                      ->nullable();

            }

            if (!Schema::hasColumn('users','foto')) {

                $table->string('foto')
                      ->nullable();

            }

            if (!Schema::hasColumn('users','last_login')) {

                $table->timestamp('last_login')
                      ->nullable();

            }

        });

    }

    public function down(): void
    {

        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users','jabatan')) {

                $table->dropColumn('jabatan');

            }

            if (Schema::hasColumn('users','divisi')) {

                $table->dropColumn('divisi');

            }

            if (Schema::hasColumn('users','foto')) {

                $table->dropColumn('foto');

            }

            if (Schema::hasColumn('users','last_login')) {

                $table->dropColumn('last_login');

            }

        });

    }

};