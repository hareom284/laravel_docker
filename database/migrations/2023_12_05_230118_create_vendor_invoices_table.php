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
        Schema::create('vendor_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable();
            $table->float('payment_amt')->nullable();
            $table->float('discount_percentage')->nullable();
            $table->float('discount_amt')->nullable();
            $table->float('credit_amt')->nullable();
            $table->string('document_file')->nullable();
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('vendor_id');
            $table->unsignedInteger('purchase_order_id')->nullable();
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
        Schema::dropIfExists('vendor_invoices');
    }
};
