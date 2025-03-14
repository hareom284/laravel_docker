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
            $table->string('whatsapp_language_reminder')->nullable()->after('action');
            $table->string('whatsapp_template_reminder')->nullable()->after('action');
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
            $table->dropColumn('whatsapp_language_reminder');
            $table->dropColumn('whatsapp_template_reminder');
        });
    }
};
