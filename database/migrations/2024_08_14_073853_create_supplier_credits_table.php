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
        Schema::create('supplier_credits', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->longText('description');
            $table->boolean('is_gst_inclusive')->default(false);
            $table->float('total_amount');
            $table->float('amount');
            $table->float('gst_amount')->nullable();
            $table->dateTime('invoice_date');
            $table->string('pdf_path')->nullable();
            $table->integer('quick_book_vendor_credit_id')->nullable();
            $table->unsignedInteger('vendor_id');
            $table->unsignedInteger('sale_report_id');
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
        Schema::dropIfExists('supplier_credits');
    }
};
