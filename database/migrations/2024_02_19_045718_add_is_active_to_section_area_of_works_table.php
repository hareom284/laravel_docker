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
        Schema::table('section_area_of_works', function (Blueprint $table) {
            $table->tinyInteger('is_active')->default(1)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('section_area_of_works', function (Blueprint $table) {
            $table->tinyInteger('is_active')->default(1)->after('name');
        });
    }
};
