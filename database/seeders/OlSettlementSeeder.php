<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OlSettlementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ol_settlement')->insert([
            'atek_id'          => 'AP00112324158',
            'txn_date'         => 'txn_date',
            'amount'           => 'amount',
            'eq_id'            => 'eq_id',
            'bank_order_id'    => 'bank_order_id',
            'is_settle'        =>  0,
            'settlement_date'  => 'settlement_date',
            'settlement_amt'   => 'settlement_amt',
        ]);
    }
}
