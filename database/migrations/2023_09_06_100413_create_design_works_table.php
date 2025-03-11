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
        Schema::create('design_works', function (Blueprint $table) {
            $table->id();
            $table->string('document_date')->nullable();
            $table->string('name');
            $table->string('document_file');
            $table->string('scale')->nullable();
            $table->integer('request_status')->nullable();
            $table->dateTime('signed_date')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->on('projects')->onDelete('cascade');
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
        Schema::dropIfExists('design_works');
    }
};
