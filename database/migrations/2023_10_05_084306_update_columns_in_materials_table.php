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
        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['design_work_id']);
            $table->dropColumn('design_work_id');
            $table->dropColumn('description');
            $table->string('name')->after('id')->nullable();
            $table->boolean('is_predefined')->after('name')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('is_predefined');
            $table->dropColumn('name');
            $table->string('description')->after('id')->nullable();
            $table->bigInteger('design_work_id')->after('description');
        });
    }
};
