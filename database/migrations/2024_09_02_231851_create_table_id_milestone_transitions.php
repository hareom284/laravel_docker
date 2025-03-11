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
        Schema::create('id_milestone_transitions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->unsignedBigInteger('from_id_milestone_id')->nullable();
            $table->unsignedBigInteger('to_id_milestone_id')->nullable();
            $table->string('action')->nullable();
            $table->timestamps();

            $table->foreign('from_id_milestone_id')
                ->references('id')->on('id_milestones')
                ->onDelete('set null');

            $table->foreign('to_id_milestone_id')
                ->references('id')->on('id_milestones')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('id_milestone_transitions');
    }
};
