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
        Schema::table('contracts', function (Blueprint $table) {
            $table->longText('stages_of_renovation')->nullable()->after('contractor_signature');
            $table->longText('liability_period')->nullable()->after('stages_of_renovation');
            $table->string('employer_witness_name')->nullable()->after('liability_period');
            $table->string('contractor_witness_name')->nullable()->after('employer_witness_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            //
        });
    }
};
