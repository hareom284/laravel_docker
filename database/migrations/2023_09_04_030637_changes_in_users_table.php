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
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rank_id']);
            $table->dropForeign(['company_id']);
            $table->dropColumn(['user_type','nric','rank_id','company_id','role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('user_type');
            $table->string('nric');
            $table->foreignId('company_id')->nullable()->constrained()->on('companies')->onDelete('cascade');
            $table->foreignId('rank_id')->nullable()->constrained()->on('ranks')->onDelete('cascade');
        });
    }
};
