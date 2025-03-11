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
        Schema::table('renovation_documents', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'customer_signed'])
            ->after('printable_pdf')
            ->default('approved')
            ->comment('These feature is used for pending and approval from management role where sale person meed approval from manager before customer singed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('renovation_documents', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
