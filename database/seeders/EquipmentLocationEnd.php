<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentLocationEnd extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('ms_equipment_location')->insert([
            'location_name' => "Versova End",
            'description' => "Versova End"
        ]);

        DB::table('ms_equipment_location')->insert([
            'location_name' => "Ghatkopar End",
            'description' => "Ghatkopar End"
        ]);

        DB::table('ms_equipment_location')->insert([
            'location_name' => "IR End",
            'description' => "Indian Railway End"
        ]);


    }
}
