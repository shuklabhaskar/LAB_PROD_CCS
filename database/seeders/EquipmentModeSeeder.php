<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentModeSeeder extends Seeder
{
    public function run()
    {
        DB::table('ms_equipment_mode') -> insert(['eq_mode_name' => 'ENTRY','eq_type_id' => 1,'description'=>'ENTRY']);
        DB::table('ms_equipment_mode') -> insert(['eq_mode_name' => 'EXIT','eq_type_id' => 1,'description'=>'EXIT']);
        DB::table('ms_equipment_mode') -> insert(['eq_mode_name' => 'BI-DI','eq_type_id' => 1,'description'=>'BI-DI']);
    }
}

