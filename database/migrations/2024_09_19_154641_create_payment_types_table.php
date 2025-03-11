<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $paymentLists = [
            ['id' => 1, 'name' => 'Deposit'],
            ['id' => 2, 'name' => '1st Payment Type'],
            ['id' => 3, 'name' => '2nd Payment Type'],
            ['id' => 4, 'name' => '3rd Payment Type'],
            ['id' => 5, 'name' => 'Final Payment Type'],
        ];
        DB::table('payment_types')->insert($paymentLists);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_types');
    }
};
