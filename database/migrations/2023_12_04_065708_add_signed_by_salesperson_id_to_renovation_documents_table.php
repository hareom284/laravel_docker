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
            $table->unsignedBigInteger('signed_by_salesperson_id')->after('salesperson_signature')->nullable()->on('staffs')->onDelete('cascade');
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
            //
        });
    }
};
