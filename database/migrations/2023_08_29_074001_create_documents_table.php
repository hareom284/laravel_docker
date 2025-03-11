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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->string('title');
            $table->string('document_file')->nullable();
            $table->boolean('allow_customer_view')->default(false);
            $table->string('file_type')->nullable();
            $table->foreignId('folder_id')->nullable()->constrained()->on('folders')->onDelete('cascade');  
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
        Schema::dropIfExists('documents');
    }
};
