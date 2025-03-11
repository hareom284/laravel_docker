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
        Schema::create('salesperson_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salesperson_id')->nullable()->constrained()->on('users')->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->on('projects')->onDelete('cascade');
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
        Schema::dropIfExists('salesperson_projects');
    }
};
