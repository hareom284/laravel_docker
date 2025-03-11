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
        Schema::table('3d_designs', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->renameColumn('drafter_id', 'uploader_id');
            $table->date('last_edited')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('3d_designs', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->renameColumn('uploader_id', 'drafter_id');
        });
    }
};
