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
        Schema::table('salesperson_monthly_kpi_records', function (Blueprint $table) {
            $table->renameColumn('salesperson_id', 'saleperson_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salesperson_monthly_kpi_records', function (Blueprint $table) {
            $table->renameColumn('saleperson_id', 'salesperson_id');
        });
    }
};
