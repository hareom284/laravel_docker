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
            $table->float('amended_amt')->after('payment_amt')->nullable()->comment('Amended Amount');
            $table->text('remark')->after('payment_amt')->nullable()->comment('Remark or Note');
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
            $table->dropColumn('amended_amt');
            $table->dropColumn('remark');
        });
    }
};
