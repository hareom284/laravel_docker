<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('id_milestone','id_milestone_id');
            $table->renameColumn('rejected_reason','rejected_reason_id');
        });

        Artisan::call('transfer:id-milestone-data');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('id_milestone_id','id_milestone');
            $table->renameColumn('rejected_reason_id','rejected_reason');
        });
    }
};
