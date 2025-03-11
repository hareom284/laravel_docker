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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nric')->nullable();
            $table->integer('status');
            $table->unsignedBigInteger('assigned_by_management_id')->nullable();
            $table->foreignId('user_id')->constrained()->on('users')->onDelete('cascade');
        });

        // Status Value
        // LEAD = 1
        // HOMEOWNER = 2
        // COMPLETED = 3
        // INACTIVE = 4

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
