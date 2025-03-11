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
        Schema::create('document_approvers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('renovation_document_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('renovation_document_id')
                ->references('id')
                ->on('renovation_documents')
                ->onDelete('cascade');
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_approvers');
    }
};
