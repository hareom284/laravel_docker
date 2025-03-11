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
        Schema::table('quotation_template_items', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['measurement_id']);

            // Drop the measurement_id column
            $table->dropColumn('measurement_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation_template_items', function (Blueprint $table) {
            // Add the measurement_id column
            $table->unsignedBigInteger('measurement_id')->after('unit_of_measurement')->nullable();

            // Add the foreign key constraint
            $table->foreign('measurement_id')->references('id')->on('measurement');
        });
    }
};
