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
            $table->double('cost_price')->after('price')->nullable()->default(0);
            $table->double('profit_margin')->after('cost_price')->nullable()->default(0);
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
            $table->dropColumn('cost_price');
            $table->dropColumn('profit_margin');
        });
    }
};
