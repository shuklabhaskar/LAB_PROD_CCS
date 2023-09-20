<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcqParamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('acq_param')
            -> insert([
                'acq_param_id'  => 1,
                'operator_id'   =>'1',
                'acq_id'        =>'1',
                'acq_name'      =>'FIEG',
                'acq_mid'       => "Mumbai80360927499450",
                'client_id'     => "FIEG0N001",
                'line_id'       => 1,
                'description'   => 'FIEG',
            ]);

    }
}
