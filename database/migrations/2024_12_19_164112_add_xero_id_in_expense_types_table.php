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
        Schema::table('expense_types', function (Blueprint $table) {
            $table->integer('quick_book_id')->nullable()->change();
            $table->string('xero_id')->nullable()->after('quick_book_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_types', function (Blueprint $table) {
            $table->integer('quick_book_id')->change();
            $table->dropColumn('xero_id');
        });
    }
};
