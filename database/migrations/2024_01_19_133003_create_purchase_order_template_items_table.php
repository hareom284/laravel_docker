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
        Schema::create('purchase_order_template_items', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('quantity')->nullable();
            $table->string('code')->nullable();
            $table->string('size')->nullable();
            $table->unsignedInteger('vendor_category_id');
            $table->unsignedInteger('company_id');
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
        Schema::dropIfExists('purchase_order_template_items');
    }
};
