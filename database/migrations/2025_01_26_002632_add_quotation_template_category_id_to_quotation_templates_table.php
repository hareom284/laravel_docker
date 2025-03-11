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
        Schema::table('quotation_templates', function (Blueprint $table) {
            $table->foreignId('quotation_template_category_id')
                ->nullable()
                ->after('name')
                ->constrained('quotation_template_categories')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation_templates', function (Blueprint $table) {
            $table->dropForeign(['quotation_template_category_id']);
            $table->dropColumn('quotation_template_category_id');
        });
    }
};
