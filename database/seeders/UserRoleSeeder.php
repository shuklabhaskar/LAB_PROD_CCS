<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_user_role') -> insert(['user_role_name'=>'Admin',     'description'=>'For Admin',     'created_by'=>1,]);
        DB::table('ms_user_role') -> insert(['user_role_name'=>'AFC',       'description'=>'For AFC',       'created_by'=>1,]);
        DB::table('ms_user_role') -> insert(['user_role_name'=>'TSO',       'description'=>'For TSO',       'created_by'=>1,]);
        DB::table('ms_user_role') -> insert(['user_role_name'=>'CCO',       'description'=>'For CCO',       'created_by'=>1,]);
        DB::table('ms_user_role') -> insert(['user_role_name'=>'SSP',       'description'=>'For SSP',       'created_by'=>1,]);
        DB::table('ms_user_role') -> insert(['user_role_name'=>'AFC Maint', 'description'=>'For AFC Maint', 'created_by'=>1,]);
    }
}
