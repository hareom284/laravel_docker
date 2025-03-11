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
            $table->tinyInteger('is_active')->default(1)->after('profit_margin');
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
            $table->dropColumn('is_active');
        });
    }
};
