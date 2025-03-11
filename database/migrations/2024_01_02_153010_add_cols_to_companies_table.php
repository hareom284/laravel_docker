<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('docu_prefix')->nullable()->after('company_stamp');
            $table->integer('invoice_no_start')->nullable()->after('docu_prefix');
            $table->date('fy_start')->nullable()->after('invoice_no_start');
            $table->date('fy_end')->nullable()->after('fy_start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
};
