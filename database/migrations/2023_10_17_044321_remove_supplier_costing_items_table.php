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
        Schema::dropIfExists('supplier_costing_items');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('supplier_costing_items', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('invoice_no');
            $table->double('payment_amt');
            $table->double('discount_percent');
            $table->double('discount_amt');
            $table->double('credit_amt');
            $table->string('document_file');
            $table->longText('remark')->nullable();
            $table->unsignedInteger('sale_report_id');
            $table->timestamps();
        });
    }
};
