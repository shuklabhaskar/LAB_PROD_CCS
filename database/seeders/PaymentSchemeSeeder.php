<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSchemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_payment_scheme')-> insert(['ps_name'=>'MMOPL',      'description'=>'Mumbai Metro One Limited']);
        DB::table('ms_payment_scheme')-> insert(['ps_name'=>'RUPAY',      'description'=>'Rupay Cards']);
        DB::table('ms_payment_scheme')-> insert(['ps_name'=>'MASTERCARD', 'description'=>'MasterCards']);
        DB::table('ms_payment_scheme')-> insert(['ps_name'=>'VISA',       'description'=>'Visa Card']);
    }
}
