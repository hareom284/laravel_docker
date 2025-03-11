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
        Schema::create('hdb_acknowledgement_forms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->string('name')->nullable();
            $table->date('date_uploaded')->nullable();
            $table->string('document_file')->nullable();
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
        Schema::dropIfExists('hdb_acknowledgement_forms');
    }
};
