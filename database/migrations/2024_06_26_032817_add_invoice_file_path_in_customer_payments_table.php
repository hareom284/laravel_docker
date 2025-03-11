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
            $table->string('unpaid_invoice_file_path')->nullable()->after('remark');
            $table->string('paid_invoice_file_path')->nullable()->after('unpaid_invoice_file_path');
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
            $table->dropColumn('unpaid_invoice_file_path');
            $table->dropColumn('paid_invoice_file_path');
        });
    }
};
