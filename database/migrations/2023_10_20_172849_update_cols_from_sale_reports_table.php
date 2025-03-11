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
        Schema::table('sale_reports', function (Blueprint $table) {
            $table->double('total_cost')->nullable()->change();
            $table->integer('total_sales')->nullable()->change();
            $table->integer('comm_issued')->nullable()->change();
            $table->integer('special_discount')->nullable()->change();
            $table->string('gst')->nullable()->change();
            $table->string('rebate')->nullable()->change();
            $table->string('net_profit_and_loss')->nullable()->change();
            $table->string('carpentry_job_amount')->nullable()->change();
            $table->string('carpentry_cost')->nullable()->change();
            $table->string('carpentry_comm')->nullable()->change();
            $table->string('carpentry_special_discount')->nullable()->change();
            $table->integer('net_profit')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_reports', function (Blueprint $table) {
            //
        });
    }
};
