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
            $table->float('total_cost')->change();
            $table->float('total_sales')->change();
            $table->float('comm_issued')->change();
            $table->float('special_discount')->change();
            $table->float('gst')->change();
            $table->float('rebate')->change();
            $table->float('net_profit_and_loss')->change();
            $table->float('carpentry_job_amount')->change();
            $table->float('carpentry_cost')->change();
            $table->float('carpentry_comm')->change();
            $table->float('carpentry_special_discount')->change();
            $table->float('net_profit')->change();
            $table->float('or_issued')->nullable()->after('comm_issued');
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
