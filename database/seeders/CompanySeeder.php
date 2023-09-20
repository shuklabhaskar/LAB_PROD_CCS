<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_company_inv') -> insert(['company_name' => 'MMOPL','description' => 'New CompanySeeder','start_date'=>'2021-10-22 11:52:16']);
    }
}
