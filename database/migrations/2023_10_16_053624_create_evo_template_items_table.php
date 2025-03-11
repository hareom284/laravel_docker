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
        Schema::create('evo_template_items', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->double('unit_rate_without_gst')->nullable();
            $table->double('unit_rate_with_gst')->nullable();
            $table->bigInteger('salesperson_id')->nullable()->on('staffs')->onDelete('cascade');
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
        Schema::dropIfExists('evo_template_items');
    }
};
