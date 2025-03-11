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
        Schema::create('evo_item_rooms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('item_id')->on('evo_items')->onDelete('cascade');
            $table->bigInteger('room_id')->on('evo_template_rooms')->onDelete('cascade');
            $table->integer('quantity');
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
        Schema::dropIfExists('evo_item_rooms');
    }
};
