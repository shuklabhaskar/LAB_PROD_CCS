<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlacklistReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST blacklisted - Lost/Stolen Card",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST blacklisted - Faulty Card",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST Blacklisted",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "Reserved for Bank Top-up Processing",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST blacklisted - bad debt",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST blacklisted - bank account closed",
        ]);


        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST entry/exit upgrade alert",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST Surrendered",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "Pre-encoded CSC/CST Blocked from use",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "Pre-encoded CSC.CST deposit to be Collected",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "Suspect CSC/CST Processing",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST not Issued",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST not Registered at Issuer",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "Excessive Purse Value",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST Life Expired",
        ]);

        DB::table('ms_cl_blacklist_reason')->insert([
            'reason' => "CSC/CST Suspended due to Customer Request",
        ]);
    }
}
