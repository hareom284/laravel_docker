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
        Schema::table('salespersons_customers', function (Blueprint $table) {
            $table->dropForeign(['salesperson_uid']);
            $table->foreign('salesperson_uid')->references('id')->on('staffs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salespersons_customers', function (Blueprint $table) {
            //
        });
    }
};
