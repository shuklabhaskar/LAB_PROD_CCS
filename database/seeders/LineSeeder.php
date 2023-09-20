<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_line_inventory')
            -> insert(['line_name' => 'VER-GHA','company_id' => 1,'description' => 'LINE ONE','start_date' => '2021-10-22 11:52:16',]);
    }
}
