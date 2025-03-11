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
        Schema::create('vendor_debits', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no');
            $table->text('description');
            $table->boolean('is_gst_inclusive');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->decimal('gst_amount', 15, 2);
            $table->string('pdf_path')->nullable();
            $table->date('invoice_date');
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->foreignId('sale_report_id')->constrained('sale_reports');
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
        Schema::dropIfExists('vendor_debits');
    }
};
