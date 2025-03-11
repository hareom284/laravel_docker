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
        Schema::create('handover_certificate_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('handover_certificate_id')->nullable()->constrained()->on('handover_certificates')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->on('users')->onDelete('cascade');
            $table->string('customer_signature')->nullable();
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
        Schema::dropIfExists('handover_certificate_signatures');
    }
};
