<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        DB::table('user_inventory') -> insert(['user_id'=> 1, 'first_name'=> "BHASKAR", 'middle_name'=>"RAKESH KUMAR", 'last_name'     => "SHUKLA", 'designation'   => "ENGINEER", 'emp_id'        => "300017", 'emp_mobile'    => "9167003343", 'emp_email'     => "bhaskarshukla274@gmail.com", 'emp_gender'    => 1, 'emp_dob'       => "2000-05-21", 'description'   => "ENGINEER", 'user_login'    => "9167", 'user_pwd'      => "9167", 'status'        => 1, 'user_role_id'  => 1, 'start_date'    => "2022-10-08 12:30:49", 'is_test'       => 0, 'created_by'    => 1, 'created_at'    => "2022-10-08 12:30:49",]);

    }
}
