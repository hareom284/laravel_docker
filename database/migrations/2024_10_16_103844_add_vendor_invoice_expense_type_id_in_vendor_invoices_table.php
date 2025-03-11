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
        Schema::table('vendor_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_invoice_expense_type_id')->nullable()->after('vendor_payment_id');

            $table->foreign('vendor_invoice_expense_type_id')->references('id')->on('vendor_invoice_expense_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_invoices', function (Blueprint $table) {

            $table->dropForeign(['vendor_invoice_expense_type_id']);

            $table->dropColumn('vendor_invoice_expense_type_id');
        });
    }
};
