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
        Schema::create('salespersons_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salesperson_uid')->constrained()->on('users')->onDelete('cascade');
            $table->foreignId('customer_uid')->constrained()->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salespersons_customers');
    }
};
