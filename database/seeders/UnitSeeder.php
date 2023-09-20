<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_units') -> insert(['unit_id' => 1,'unit_name' => 'Sec','description'  =>'Unit For Second']);
        DB::table('ms_units') -> insert(['unit_id' => 2,'unit_name' => 'Min','description'  =>'Unit For Min']);
        DB::table('ms_units') -> insert(['unit_id' => 3,'unit_name' => 'Hour','description' =>'unit For Hours ']);
        DB::table('ms_units') -> insert(['unit_id' => 4,'unit_name' => 'Day','description'  =>'unit For Day ']);
    }
}
