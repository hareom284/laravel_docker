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
        Schema::create('saleperson_yearly_kpi_records', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('management_target')->nullable();
            $table->unsignedBigInteger('saleperson_id')->on('staffs')->onDelete('cascade');
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
        Schema::dropIfExists('saleperson_yearly_kpi_records');
    }
};
