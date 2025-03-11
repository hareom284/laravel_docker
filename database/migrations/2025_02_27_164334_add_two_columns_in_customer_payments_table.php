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
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->date('invoice_date')->nullable()->after('invoice_no');
            $table->string('credit_note_file_path')->nullable()->after('paid_invoice_file_path');

            $table->integer('quick_book_credit_note_id')->nullable()->after('quick_book_payment_id');
            $table->string('xero_credit_note_id')->nullable()->after('xero_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_payments', function (Blueprint $table) {
            $table->dropColumn('credit_note_file_path');
            $table->dropColumn('quick_book_credit_note_id');
            $table->dropColumn('xero_credit_note_id');
        });
    }
};
