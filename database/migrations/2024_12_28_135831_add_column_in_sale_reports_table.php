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
            $table->enum('file_status', ['DEFAULT', 'PENDING', 'MARKED', 'APPROVED'])->default('DEFAULT')->after('project_id');
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
            $table->dropColumn('file_status');
        });
    }
};
