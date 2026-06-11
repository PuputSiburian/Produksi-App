<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {

        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM(
                'admin',
                'manager'
            )
            NOT NULL
            DEFAULT 'admin'
        ");

    }

    public function down(): void
    {

        DB::statement("
            ALTER TABLE users
            MODIFY role ENUM(
                'admin',
                'manager',
                'supervisor',
                'operator_cutting',
                'operator_crimping',
                'operator_line'
            )
            NOT NULL
            DEFAULT 'operator_line'
        ");

    }

};