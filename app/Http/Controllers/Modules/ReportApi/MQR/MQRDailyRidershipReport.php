<?php

namespace App\Http\Controllers\Modules\ReportApi\MQR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MQRDailyRidershipReport extends Controller
{
    public function dailyRidership(Request $request)
    {

        set_time_limit(0);

        $from = $request->input('from_date');
        $to = $request->input('to_date');

        /* VALIDATION */
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date' => 'required|date_format:Y-m-d H:i:s',
        ]);


        /* IF VALIDATION FAILS */
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        } else {

            $MqrDailyRidership = [];

            /* TO GET NUMBER OF STATION AND STATION NAME */
            $stations = DB::table('station_inventory')
                ->select('stn_short_name', 'stn_id')
                ->orderBy('stn_id', 'ASC')
                ->get();

            $Passes = DB::table('pass_inventory')
                ->whereIn('pass_id', [10, 90])
                ->where('status', true)
                ->where('is_test', false)
                ->where('media_type_id', 2)
                ->select(['pass_name', 'pass_id'])
                ->get();


            foreach ($stations as $station) {

                $data = [
                    'stn_name' => $station->stn_short_name,
                    'stn_code' => $station->stn_id,
                ];

                foreach ($Passes as $pass) {

                    $sjtValidation = DB::table('msjt_validation')
                        ->where('pass_id', '=', $pass->pass_id)
                        ->where('msjt_validation.is_test', '=', false)
                        ->whereBetween(DB::raw('(msjt_validation.txn_date)'), [$from, $to])
                        ->where('stn_id', '=', $station->stn_id)
                        ->where('val_type_id', '=', 1)
                        ->count();

                    $rjtValidation = DB::table('mrjt_validation')
                        ->where('pass_id', '=', $pass->pass_id)
                        ->where('mrjt_validation.is_test', '=', false)
                        ->whereBetween(DB::raw('(mrjt_validation.txn_date)'), [$from, $to])
                        ->where('stn_id', '=', $station->stn_id)
                        ->where('val_type_id', '=', 1)
                        ->count();

                    $data[$pass->pass_name] = $sjtValidation + $rjtValidation;

                }

                $MqrDailyRidership[] = $data;

            }

            return response([
                'status' => true,
                'data' => $MqrDailyRidership,
            ]);

        }
    }

}
