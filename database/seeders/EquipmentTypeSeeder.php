<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        DB::table('ms_equipment_type') -> insert(['eq_type_name' => 'AG','eq_type_id'   => 1,'description'=>'FOR AG']);
        DB::table('ms_equipment_type') -> insert(['eq_type_name' => 'POST','eq_type_id'  => 2,'description'=>'FOR POST']);
        DB::table('ms_equipment_type') -> insert(['eq_type_name' => 'AVM','eq_type_id'  => 3,'description'=>'FOR AVM']);
        DB::table('ms_equipment_type') -> insert(['eq_type_name' => 'SCS','eq_type_id'  => 4,'description'=>'For SCS']);
    }
}
