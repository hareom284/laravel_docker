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
        Schema::create('term_and_condition_paragraphs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('term_and_condition_page_id');
            $table->foreign('term_and_condition_page_id', 'short_fk_tac_page_id')
                ->references('id')
                ->on('term_and_condition_pages')
                ->cascadeOnDelete();
            $table->longText('content')->nullable();
            $table->boolean('is_need_signature')->default(false);
            $table->string('signature_position')->default('left');
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
        Schema::dropIfExists('term_and_condition_paragraphs');

    }
};
