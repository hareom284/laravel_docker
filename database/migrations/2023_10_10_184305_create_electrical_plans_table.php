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
        Schema::create('electrical_plans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->onDelete('cascade');
            $table->timestamp('date_uploaded')->nullable();
            $table->string('document_file')->nullable();
            $table->string('customer_signature')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('electrical_plans');
    }
};
