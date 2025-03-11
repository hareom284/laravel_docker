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
        Schema::table('schedules', function (Blueprint $table) {

            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            
            $table->dropForeign(['id_milestone_id']);
            $table->dropColumn('id_milestone_id');
            
            $table->unsignedBigInteger('receiver_id')->after('deadline');
            $table->json('notification_types')->after('deadline');
            $table->string('whatsapp_number')->after('deadline');
            $table->string('whatsapp_template')->nullable()->after('deadline');
            $table->string('whatsapp_language')->nullable()->after('deadline');
            $table->string('title')->nullable()->after('deadline');
            $table->string('message')->nullable()->after('deadline');
            $table->string('email')->nullable()->after('deadline');
            $table->unsignedBigInteger('sender_id')->nullable()->after('deadline');

            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {

            $table->dropForeign(['receiver_id']);
            $table->dropForeign(['sender_id']);
            $table->dropColumn(['receiver_id', 'notification_types', 'whatsapp_number', 'title', 'message', 'email', 'sender_id']);

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('id_milestone_id');

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('id_milestone_id')->references('id')->on('id_milestones')->onDelete('cascade');
        });
    }
};
