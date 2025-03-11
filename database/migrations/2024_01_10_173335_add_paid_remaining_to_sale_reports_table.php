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
        Schema::table('sale_reports', function (Blueprint $table) {
            $table->double('paid')->default(0)->after('net_profit');
            $table->double('remaining')->default(0)->after('paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_reports', function (Blueprint $table) {
            $table->dropColumn('paid');
            $table->dropColumn('remaining');
        });
    }
};
