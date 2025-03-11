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
            $table->boolean('is_gst_inclusive')->after('credit_amt')->default(false);
            $table->float('gst_value')->after('is_gst_inclusive');
            $table->date('invoice_date')->after('is_gst_inclusive')->nullable();
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
            $table->dropColumn('is_gst_inclusive');
            $table->dropColumn('gst_value');
            $table->dropColumn('invoice_date');
        });
    }
};
