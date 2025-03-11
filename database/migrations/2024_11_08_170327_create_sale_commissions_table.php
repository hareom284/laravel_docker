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
        Schema::create('sale_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_report_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('commission_percent', 5, 2);
            $table->timestamps();

            $table->foreign('sale_report_id')->references('id')->on('sale_reports')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_commissions');
    }
};
