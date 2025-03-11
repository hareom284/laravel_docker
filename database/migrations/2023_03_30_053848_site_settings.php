<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string("site_name");
            $table->string("ssl")->nullable();
            $table->string("timezone");
            $table->string('locale');
            $table->string("email");
            $table->string("contact_number");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
