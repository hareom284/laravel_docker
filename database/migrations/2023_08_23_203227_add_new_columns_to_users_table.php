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
            $table->string('profile_pic')->nullable();
            $table->string('name_prefix')->nullable();
            $table->string('nric')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->on('companies')->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained()->on('roles')->onDelete('cascade');
            $table->foreignId('rank_id')->nullable()->constrained()->on('ranks')->onDelete('cascade');
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
            //
        });
    }
};
