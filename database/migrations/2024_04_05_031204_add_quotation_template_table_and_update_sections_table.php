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
        // Create the quotation_templates table
        Schema::create('quotation_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salesperson_id')->nullable();
            $table->string('name')->nullable();
            $table->timestamps();
            $table->softdeletes();
        });

        // Add quotation_template_id column to the sections table
        Schema::table('sections', function (Blueprint $table) {
            $table->unsignedBigInteger('quotation_template_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the quotation_template_id column from the sections table
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('quotation_template_id');
        });

        // Drop the quotation_templates table
        Schema::dropIfExists('quotation_templates');   
     }
};
