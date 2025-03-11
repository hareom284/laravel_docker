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
        Schema::table('id_milestones', function (Blueprint $table) {
            $table->enum('status', ['PENDING', 'DONE', 'REJECTED'])->nullable()->after('color_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('id_milestones', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
