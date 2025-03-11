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
        Schema::create('sale_reports', function (Blueprint $table) {
            $table->id();
            $table->double('total_cost');
            $table->integer('total_sales');
            $table->integer('comm_issued');
            $table->integer('special_discount');
            $table->string('gst');
            $table->string('rebate');
            $table->string('net_profit_and_loss');
            $table->string('carpentry_job_amount');
            $table->string('carpentry_cost');
            $table->string('carpentry_comm');
            $table->string('carpentry_special_discount');
            $table->integer('net_profit');
            $table->unsignedInteger('project_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_reports');
    }
};
