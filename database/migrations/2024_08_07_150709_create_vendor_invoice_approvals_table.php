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
        Schema::create('vendor_invoice_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_invoice_id');
            $table->foreign('vendor_invoice_id')->references('id')->on('vendor_invoices')->onDelete('cascade');

            $table->unsignedBigInteger('approved_by');
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
        Schema::dropIfExists('vendor_invoice_approvals');
    }
};
