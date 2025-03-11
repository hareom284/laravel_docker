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
        Schema::table('handover_certificates', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
            $table->dateTime('last_edited')->nullable()->change();
            $table->string('customer_signature')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('null_in_handover_certificates', function (Blueprint $table) {
            //
        });
    }
};
