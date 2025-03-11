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
    public $tableName = 'renovation_documents';

    public function up()
    {
        DB::statement("
        ALTER TABLE {$this->tableName}
        MODIFY COLUMN status ENUM('pending', 'approved', 'customer_signed', '1st_approved')
        NOT NULL DEFAULT 'approved'
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
        ALTER TABLE {$this->tableName}
        MODIFY COLUMN status ENUM('pending', 'approved', 'customer_signed')
        NOT NULL DEFAULT 'approved'
    ");
    }
};
