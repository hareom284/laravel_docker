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
            $table->boolean('is_CN')->default(false)->after('is_FOC');
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
            $table->dropColumn('is_CN');
        });
    }
};
