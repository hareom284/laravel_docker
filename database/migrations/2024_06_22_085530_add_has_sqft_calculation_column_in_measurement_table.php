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
        Schema::table('measurement', function (Blueprint $table) {
            $table->boolean('has_sqft_calculation')->after('fixed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('measurement', function (Blueprint $table) {
            $table->dropColumn('has_sqft_calculation');
        });
    }
};
