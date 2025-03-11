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
            $table->string('message')->nullable()->after('action');
            $table->string('title')->nullable()->after('action');
            $table->string('whatsapp_language')->nullable()->after('action');
            $table->string('whatsapp_template')->nullable()->after('action');
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
            $table->dropColumn('message');
            $table->dropColumn('title');
            $table->dropColumn('whatsapp_language');
            $table->dropColumn('whatsapp_template');
        });
    }
};
