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
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('street_name')->nullable()->change();
            $table->string('block_num')->nullable()->change();
            $table->string('postal_code')->nullable()->change();
            $table->string('street_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('email')->change();
            $table->string('street_name')->change();
            $table->string('block_num')->change();
            $table->string('postal_code')->change();
            $table->string('street_name')->change();
        });
    }
};
