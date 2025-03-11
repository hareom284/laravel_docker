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
        Schema::create('renovation_document_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('renovation_document_id')->nullable()->on('renovation_documents')->onDelete('cascade');
            $table->string('setting', 50);
            $table->mediumText('value');
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
        Schema::dropIfExists('renovation_document_settings');
    }
};
