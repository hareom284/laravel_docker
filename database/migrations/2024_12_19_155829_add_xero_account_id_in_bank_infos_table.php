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
        Schema::table('bank_infos', function (Blueprint $table) {

            $table->integer('quick_book_account_id')->nullable()->change();
            $table->string('xero_account_id')->nullable()->after('quick_book_account_id');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_infos', function (Blueprint $table) {
            $table->integer('quick_book_account_id')->change();
            $table->dropColumn('xero_account_id');
        });
    }
};
