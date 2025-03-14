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
        Schema::table('renovation_item_sections', function (Blueprint $table) {
            $table->integer('total_items_count')->after('total_price')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renovation_item_sections', function (Blueprint $table) {
            $table->dropColumn('total_items_count');
        });
    }
};
