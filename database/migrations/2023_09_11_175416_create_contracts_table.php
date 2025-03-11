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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->string('document_file')->nullable();
            $table->string('footer_text')->nullable();
            $table->string('contract_sum')->nullable();
            $table->string('contractor_payment')->nullable();
            $table->string('owner_signature')->nullable();
            $table->string('contractor_signature')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->on('projects')->onDelete('cascade');
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
        Schema::dropIfExists('contracts');
    }
};
