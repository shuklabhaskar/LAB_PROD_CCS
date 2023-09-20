<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PassParametersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*PASS PENALTY DETAILS*/
        DB::table('ms_pass_parameters') -> insert(['params' => 'same_stn_over_time_limit','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'same_stn_over_time_pen','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'same_stn_over_time_max_pen','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'other_stn_over_time_limit','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'other_stn_over_time_pen','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'other_stn_over_time_max_pen','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'over_travel_pen','sjt' =>1,'rjt'=>1,'sv'=>0,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'entry_mismatch_limit','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'entry_mismatch_same_time_pen','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'entry_mismatch_no_exit_pen','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'exit_mismatch_pen','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'entry_exit_control','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        /* PASS PARAMETER SECTION */
        DB::table('ms_pass_parameters') -> insert(['params' => 'is_test','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'entry_validity_period','sjt' =>1,'rjt'=>1,'sv'=>0,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'return_validity_period','sjt' =>0,'rjt'=>1,'sv'=>0,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'pass_validity_period','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'grace_period','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>0]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'trip_count','sjt' =>0,'rjt'=>0,'sv'=>0,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'daily_trip_limit','sjt' =>0,'rjt'=>0,'sv'=>0,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'reload_permit','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'refund_permit','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'refund_charges','sjt' =>1,'rjt'=>1,'sv'=>1,'tp'=>1]);
        /*CARD FEE SECTION*/
        DB::table('ms_pass_parameters') -> insert(['params' => 'card_fee','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'card_fee_refund_permit','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>1]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'card_fee_refund_charges','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>1]);
        /*SV BALANCE SECTION*/
        DB::table('ms_pass_parameters') -> insert(['params' => 'min_sv_topup_amt','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>0]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'sv_step_topup_amt','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>0]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'min_sv_entry_bal','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>0]);
        DB::table('ms_pass_parameters') -> insert(['params' => 'max_sv_bal','sjt' =>0,'rjt'=>0,'sv'=>1,'tp'=>0]);
    }
































}
