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
        Schema::table('evo_item_rooms', function (Blueprint $table) {
            $table->date('start_date')->after('quantity')->nullable();
            $table->date('end_date')->after('start_date')->nullable();
            $table->tinyInteger('is_checked')->after('end_date')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evo_item_rooms', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('is_checked');
        });
    }
};
