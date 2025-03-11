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
        Schema::create('quotation_template_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('salesperson_id')->nullable();
            $table->string('description');
            $table->string('unit_of_measurement')->nullable();
            $table->bigInteger('section_id');
            $table->bigInteger('area_of_work_id')->nullable();
            $table->double('price_without_gst')->nullable();
            $table->double('price_with_gst')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('quotation_template_items');
    }
};
