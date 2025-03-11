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
        Schema::table('document_standards', function (Blueprint $table) {
            $table->longText('terms_and_conditions')->after('disclaimer')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_standards', function (Blueprint $table) {
            $table->dropColumn('terms_and_conditions');
        });
    }
};
