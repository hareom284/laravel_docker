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
        Schema::create('3d_designs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->onDelete('cascade');
            $table->bigInteger('design_work_id')->onDelete('cascade');
            $table->bigInteger('drafter_id')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->string('document_file')->nullable();
            $table->date('last_edited');
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
        Schema::dropIfExists('3d_designs');
    }
};
