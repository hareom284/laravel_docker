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
        Schema::create('id_milestones', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('message_type')->nullable();
            $table->string('user_type')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('index')->nullable();
            $table->string('color_code')->nullable();
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
        Schema::dropIfExists('id_milestones');
    }
};
