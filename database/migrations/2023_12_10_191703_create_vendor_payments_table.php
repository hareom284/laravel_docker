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
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->id();
            $table->string('bank_transaction_id');
            $table->dateTime('payment_date');
            $table->integer('payment_type');
            $table->float('amount');
            $table->string('remark');
            $table->integer('payment_method');
            $table->integer('status');
            $table->unsignedInteger('vendor_invoice_id');
            $table->unsignedInteger('payment_made_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_payments');
    }
};
