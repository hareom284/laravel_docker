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
        Schema::create('referrer_forms', function (Blueprint $table) {
            $table->id();
            $table->json('referrer_properties')->nullable();
            $table->string('owner_signature')->nullable();
            $table->string('salesperson_signature')->nullable();
            $table->string('management_signature')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('referrer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('signed_by_salesperson_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('signed_by_management_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_of_referral')->nullable();
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
        Schema::dropIfExists('referrer_forms');
    }
};
