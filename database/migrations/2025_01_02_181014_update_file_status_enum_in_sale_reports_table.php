<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        ALTER TABLE sale_reports
        MODIFY COLUMN file_status ENUM('DEFAULT', 'UPLOADED', 'PENDING', 'MARKED', 'APPROVED')
        NOT NULL DEFAULT 'DEFAULT'
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE sale_reports
            MODIFY COLUMN file_status ENUM('DEFAULT', 'PENDING', 'MARKED', 'APPROVED')
            NOT NULL DEFAULT 'DEFAULT'
        ");
    }
};
