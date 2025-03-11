<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentLists = [
            ['id' => 1, 'name' => 'Deposit'],
            ['id' => 2, 'name' => '1st Payment Type'],
            ['id' => 3, 'name' => '2nd Payment Type'],
            ['id' => 4, 'name' => '3rd Payment Type'],
            ['id' => 5, 'name' => 'Final Payment Type'],
        ];
        DB::table('payment_types')->insert($paymentLists);
    }
}
