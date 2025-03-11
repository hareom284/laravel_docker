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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable();
            $table->string('description')->nullable();
            $table->date('collection_of_keys')->nullable();
            $table->date('expected_date_of_completion')->nullable();
            $table->date('completed_date')->nullable();
            $table->string('project_status')->nullable();
            $table->string('created_by')->nullable();
            $table->string('agreement_no')->nullable();
            $table->date('customer_signed_contract_and_quotation_date')->nullable();
            $table->foreignId('company_id')->nullable()->constrained()->on('companies')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->on('users')->onDelete('cascade');
            $table->foreignId('property_id')->nullable()->constrained()->on('properties')->onDelete('cascade');
            $table->softDeletes();
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
        Schema::dropIfExists('projects');
    }
};
