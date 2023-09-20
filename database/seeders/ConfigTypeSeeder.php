<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_config_type') -> insert(['config_id' => 1,'for_config'=>0, 'config_name' => 'Equipment',          'description'=>'Equipment']);
        DB::table('ms_config_type') -> insert(['config_id' => 2,'for_config'=>1, 'config_name' => 'Fare',               'description'=>'Fare']);
        DB::table('ms_config_type') -> insert(['config_id' => 3,'for_config'=>0, 'config_name' => 'Station',            'description'=>'Station']);
        DB::table('ms_config_type') -> insert(['config_id' => 4,'for_config'=>1, 'config_name' => 'Pass',               'description'=>'Pass']);
        DB::table('ms_config_type') -> insert(['config_id' => 5,'for_config'=>0, 'config_name' => 'User',               'description'=>'User']);
        DB::table('ms_config_type') -> insert(['config_id' => 6,'for_config'=>1, 'config_name' => 'Equipment Blacklist','description'=>'Equipment Blacklist']);
        DB::table('ms_config_type') -> insert(['config_id' => 7,'for_config'=>1, 'config_name' => 'Card Blacklist',     'description'=>'Card Blacklist']);
    }
}
