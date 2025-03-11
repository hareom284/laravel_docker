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
            $table->dropColumn('document_file');
            $table->dropColumn('stages_of_renovation');
            $table->dropColumn('liability_period');
            $table->dropColumn('footer_text');
            $table->string('name')->nullable()->after('date');
            $table->string('nric')->nullable()->after('name');
            $table->string('company')->nullable()->after('nric');
            $table->string('law')->nullable()->after('company');
            $table->string('address')->nullable()->after('law');
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
            $table->dropColumn('name');
            $table->dropColumn('nric');
            $table->dropColumn('company');
            $table->dropColumn('law');
            $table->dropColumn('address');
            $table->longText('stages_of_renovation')->nullable()->after('contractor_signature');
            $table->longText('liability_period')->nullable()->after('stages_of_renovation');
            $table->string('footer_text')->nullable()->after('date');
            $table->string('document_file')->nullable()->after('date');
        });
    }
};
