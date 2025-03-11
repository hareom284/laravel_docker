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
        Schema::create('term_and_condition_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('term_and_condition_paragraph_id');
            $table->foreign('term_and_condition_paragraph_id', 'short_fk_tac_paragraph_id')
                ->references('id')
                ->on('term_and_condition_paragraphs')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->json('customer_signatures')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_and_condition_signatures');
    }
};
