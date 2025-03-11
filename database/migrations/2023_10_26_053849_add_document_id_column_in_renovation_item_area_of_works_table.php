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
        Schema::table('renovation_item_area_of_works', function (Blueprint $table) {
            $table->bigInteger('document_id')->nullable()->after('section_area_of_work_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renovation_item_area_of_works', function (Blueprint $table) {
            $table->dropColumn('document_id');
        });
    }
};
