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
        Schema::create('evos', function (Blueprint $table) {
            $table->id();
            $table->string('version_number')->nullable();
            $table->double('total_amount');
            $table->string('salesperson_signature')->nullable();
            $table->string('customer_signature')->nullable();
            $table->date('signed_date')->nullable();
            $table->string('additional_notes')->nullable();
            $table->bigInteger('project_id')->on('projects')->onDelete('cascade');
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
        Schema::dropIfExists('evos');
    }
};
