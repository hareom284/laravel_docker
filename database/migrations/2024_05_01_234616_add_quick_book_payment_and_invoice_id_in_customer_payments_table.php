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
            $table->integer('quick_book_invoice_id')->nullable()->after('sale_report_id');
            $table->integer('quick_book_payment_id')->nullable()->after('quick_book_invoice_id');
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
            $table->dropColumn('quick_book_invoice_id');
            $table->dropColumn('quick_book_payment_id');
        });
    }
};
