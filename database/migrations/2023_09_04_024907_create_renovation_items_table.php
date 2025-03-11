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
        Schema::create('renovation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renovation_document_id')->nullable()->on('renovation_documents')->onDelete('cascade');
            $table->bigInteger('cancellation_id')->nullable()->on('renovation_documents')->onDelete('cascade');
            $table->bigInteger('prev_item_id')->nullable()->on('renovation_items')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->on('projects')->onDelete('cascade');
            $table->string('name');
            $table->bigInteger('renovation_item_section_id')->nullable();
            $table->bigInteger('renovation_item_area_of_work_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('price')->nullable();
            $table->string('unit_of_measurement')->nullable();
            $table->boolean('completed')->nullable();
            $table->boolean('active')->nullable();
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
        Schema::dropIfExists('renovation_items');
    }
};
