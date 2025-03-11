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
            $table->string('contact_person')->nullable()->change();
            $table->string('contact_person_number')->nullable()->change();
            $table->string('fax_number')->nullable()->change();
            $table->double('rebate')->nullable()->change();
            $table->unsignedInteger('vendor_category_id')->nullable()->change();
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
            $table->string('contact_person')->change();
            $table->string('contact_person_number')->change();
            $table->string('fax_number')->change();
            $table->double('rebate')->change();
            $table->unsignedInteger('vendor_category_id')->change();
        });
    }
};
