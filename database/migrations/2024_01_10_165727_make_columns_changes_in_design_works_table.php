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
            $table->unsignedBigInteger('drafter_in_charge_id')->nullable()->after('request_status');
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
            $table->dropColumn('drafter_in_charge_id');
        });
    }
};
