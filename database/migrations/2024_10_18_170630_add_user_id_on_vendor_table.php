<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Src\Company\UserManagement\Infrastructure\EloquentModels\PermissionEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\RoleEloquentModel;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('email')->constrained('users')->nullOnDelete();
            $table->enum('name_prefix',['Mr','Ms','Mrs','Mdm'])->nullable()->after('email');
            $table->string('contact_person_last_name')->nullable()->after('name_prefix');
            $table->string('prefix')->nullable()->before('contact_person_number');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->dropColumn('name_prefix');
            $table->dropColumn('contact_person_last_name');
            $table->dropColumn('prefix');
        });
    }
};
