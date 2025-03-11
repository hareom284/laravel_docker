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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->string('pages');
            $table->string('sales_rep_signature');
            $table->string('manager_signature');
            $table->integer('purchase_order_number');
            $table->string('remark');
            $table->date('delivery_date');
            $table->string('delivery_time_of_the_day');
            $table->integer('status');
            $table->string('vendor_remark')->nullable();
            $table->string('invoice_no')->nullable();
            $table->float('payment_amt')->nullable();
            $table->float('discount_percentage')->nullable();
            $table->float('discount_amt')->nullable();
            $table->float('credit_amt')->nullable();
            $table->string('document_file')->nullable();
            $table->unsignedInteger('signed_by_manager_id');
            $table->unsignedInteger('project_id');
            $table->unsignedInteger('sale_rep_id');
            $table->unsignedInteger('vendor_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
};
