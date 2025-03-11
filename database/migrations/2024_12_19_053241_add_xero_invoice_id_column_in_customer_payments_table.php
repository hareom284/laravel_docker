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
            $table->string('xero_invoice_id')->nullable()->after('quick_book_payment_id');
            $table->string('xero_payment_id')->nullable()->after('xero_invoice_id');
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
            $table->dropColumn('xero_invoice_id');
            $table->dropColumn('xero_payment_id');
        });
    }
};
