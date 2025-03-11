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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger('organization_id')->nullable()
                ->references('id')
                ->on('organizations');
            $table->string('name');
            $table->date('dob')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string("contact_number")->nullable();
            $table->integer("storage_limit")->nullable();
            $table->tinyInteger("is_active")->nullable(); //
            $table->integer("stripe_id")->nullable();
            $table->string("pm_brand")->nullable();
            $table->string("pm_last_four")->nullable();
            $table->dateTime("trial_end_at")->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
