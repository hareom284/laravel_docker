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
        Schema::table('renovation_items', function (Blueprint $table) {
            $table->bigInteger('parent_id')->nullable()->after('renovation_document_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renovation_items', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
