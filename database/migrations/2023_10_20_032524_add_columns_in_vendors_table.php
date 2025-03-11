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
            $table->string('contact_person')->after('vendor_name');
            $table->integer('contact_person_number')->after('contact_person');
            $table->string('email')->after('contact_person_number');
            $table->string('street_name')->after('email');
            $table->integer('block_num')->after('street_name');
            $table->string('unit_num')->after('block_num')->nullable();
            $table->integer('postal_code')->after('unit_num');
            $table->integer('fax_number')->after('postal_code');
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
            $table->dropColumn('contact_person');
            $table->dropColumn('contact_person_number');
            $table->dropColumn('email');
            $table->dropColumn('street_name');
            $table->dropColumn('block_num');
            $table->dropColumn('unit_num');
            $table->dropColumn('postal_code');
            $table->dropColumn('fax_number');
        });
    }
};
