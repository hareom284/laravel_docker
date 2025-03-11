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
        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable()->after('salesperson_id');
        });

        Schema::table('renovation_item_sections', function (Blueprint $table) {
            $table->string('name')->nullable()->after('index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('document_id');
        });
    
        Schema::table('renovation_item_sections', function (Blueprint $table) {
            $table->dropColumn('name');
        });    }
};
