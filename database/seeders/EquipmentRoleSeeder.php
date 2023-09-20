<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentRoleSeeder extends Seeder
{

    public function run()
    {
        DB::table('ms_equipment_role') -> insert(['eq_role_id' => 1,'eq_role_name' => 'NORMAL','eq_type_id' =>1,'description'=>'NORMAL Role']);
        DB::table('ms_equipment_role') -> insert(['eq_role_id' => 2,'eq_role_name' => 'WIDE','eq_type_id'   =>1,'description'=>'WIDE Role']);
        DB::table('ms_equipment_role') -> insert(['eq_role_id' => 3,'eq_role_name' => 'TOM','eq_type_id'   =>2,'description'=>'PAID Role']);
        DB::table('ms_equipment_role') -> insert(['eq_role_id' => 4,'eq_role_name' => 'EFO','eq_type_id' =>2,'description'=>'UNPAID Role']);
    }

}
