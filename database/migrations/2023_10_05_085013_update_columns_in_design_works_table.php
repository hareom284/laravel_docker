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
        Schema::table('design_works', function (Blueprint $table) {
            $table->dateTime('date')->after('id');
            $table->dateTime('last_edited')->after('request_status')->nullable();
            $table->bigInteger('designer_in_charge_id')->after('signed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('design_works', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->dropColumn('last_edited');
            $table->dropColumn('designer_in_charge_id');
        });
    }
};
