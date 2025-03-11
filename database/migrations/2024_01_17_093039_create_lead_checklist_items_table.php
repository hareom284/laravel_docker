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
        Schema::create('lead_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_template_item_id');
            $table->unsignedBigInteger('customer_id');
            $table->boolean('status')->default(false);
            $table->date('date_completed')->nullable();
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
        Schema::dropIfExists('lead_checklist_items');
    }
};
