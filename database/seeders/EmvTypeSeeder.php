<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmvTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        DB::table('ms_emv_type')-> insert(['emv_reader_name'   =>'AG Reader',]);
        DB::table('ms_emv_type')-> insert(['emv_reader_name'   =>'TOM Reader',]);
    }
}
