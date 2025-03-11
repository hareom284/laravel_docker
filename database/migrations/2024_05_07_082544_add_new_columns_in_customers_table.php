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
        Schema::table('customers', function (Blueprint $table) {
            $table->float('budget')->nullable()->after('customer_type');
            $table->float('quote_value')->nullable()->after('budget');
            $table->float('book_value')->nullable()->after('quote_value');
            $table->date('key_collection')->nullable()->after('book_value');
            $table->string('id_milestone')->nullable()->after('key_collection');
            $table->longText('rejected_reason')->nullable()->after('id_milestone');
            $table->date('next_meeting')->nullable()->after('rejected_reason');
            $table->string('days_aging')->nullable()->after('next_meeting');
            $table->longText('remarks')->nullable()->after('days_aging');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('budget');
            $table->dropColumn('quote_value');
            $table->dropColumn('book_value');
            $table->dropColumn('key_collection');
            $table->dropColumn('id_milestone');
            $table->dropColumn('rejected_reason');
            $table->dropColumn('next_meeting');
            $table->dropColumn('days_aging');
            $table->dropColumn('remarks');
        });
    }
};
