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
            $table->double('price_without_gst')->default(0)->change();
            $table->double('price_with_gst')->default(0)->change();
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
            $table->double('price_without_gst')->nullable()->change();
            $table->double('price_with_gst')->nullable()->change();
        });
    }
};
