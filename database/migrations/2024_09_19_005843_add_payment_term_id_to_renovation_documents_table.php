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
        Schema::table('renovation_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_term_id')->nullable();
            $table->foreign('payment_term_id')->references('id')->on('payment_terms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renovation_documents', function (Blueprint $table) {
            $table->dropForeign(['payment_term_id']);
            $table->dropColumn('payment_term_id');
        });
    }
};
