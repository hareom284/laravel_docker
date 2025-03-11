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
        Schema::table('faq_items', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->after('answer')->nullable()->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('customer_id')->after('project_id')->nullable()->on('users')->onDelete('cascade');
            $table->integer('status')->after('customer_id'); // 1 = UnRead, 2 = Read
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faq_items', function (Blueprint $table) {
            //
        });
    }
};
