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
        Schema::create('renovation_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id')->nullable()->on('projects')->onDelete('cascade');
            $table->bigInteger('document_standard_id')->nullable();
            $table->string('type');
            $table->string('version_number')->nullable();
            $table->string('disclaimer')->nullable();
            $table->integer('total_amount');
            $table->date('signed_date');
            $table->string('salesperson_signature')->nullable();
            $table->string('customer_signature')->nullable();
            $table->longText('additional_notes')->nullable();
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
        Schema::dropIfExists('renovation_documents');
    }
};
