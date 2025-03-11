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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('comments')->nullable();
            $table->integer('stars');
            $table->date('date')->nullable();
            $table->unsignedBigInteger('project_id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('salesperson_id')->nullable()->on('staffs')->onDelete('cascade');
            $table->unsignedBigInteger('review_by')->nullable()->on('customers')->onDelete('cascade');
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
        Schema::dropIfExists('reviews');
    }
};
