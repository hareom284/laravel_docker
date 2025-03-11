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
        Schema::create('salesperson_monthly_kpi_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salesperson_id')->on('staffs')->onDelete('cascade');
            $table->string('year');
            $table->string('month');
            $table->string('target')->nullable();
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
        Schema::dropIfExists('salesperson_monthly_kpi_records');
    }
};
