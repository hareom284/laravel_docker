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
            $table->date('refund_date')->nullable()->after('remark');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('refund_date');
            $table->longText('refund_reason')->nullable()->after('refund_date');

            $table->tinyInteger('is_refunded')->default(0)->after('status');
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
            $table->dropColumn('refund_date');
            $table->dropColumn('refund_amount');
            $table->dropColumn('refund_reason');

            $table->dropColumn('is_refunded');
        });
    }
};
