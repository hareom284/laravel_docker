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
        Schema::create('evo_template_rooms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('salesperson_id')->nullable()->on('staffs')->onDelete('cascade');
            $table->string('room_name');
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
        Schema::dropIfExists('evo_template_rooms');
    }
};
