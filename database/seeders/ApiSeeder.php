<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/tp/exit/revenue']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/tp/stale/revenue/atek']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/tp/stale/revenue/indra']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/tp/stale/revenue/ul']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/sv/stale/revenue/indra']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/sv/stale/revenue/atek']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/sv/exit/revenue']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/audit/sv/']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/audit/tp/{startDate}/{endDate}']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/oldailyRidership']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/olCashCollection']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/olRevenue']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/olPrevDay']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/olValReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/olSaleReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/olSvAccReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/olFicoReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/clFicoReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/Daily/Ridership']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/revenue']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/cardDetail/mobileNumber']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/cl/cardDetail/cardNumber']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/clSap']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/clSvValReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/clTpValReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/clSvAccReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/clTpAccReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => '/mqr/Daily/Ridership']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'previous day','api_route' => '/mqr/PrevDay']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => 'mqr/sjtValReport']);
        DB::table('ms_api_privilege')->insert(['api_name'=>'test','api_route' => 'mqr/rjtValReport']);
    }
}
