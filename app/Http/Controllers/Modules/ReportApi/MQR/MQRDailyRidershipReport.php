<?php

namespace App\Http\Controllers\Modules\ReportApi\MQR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;


class MQRDailyRidershipReport extends Controller
{
    public function dailyRidership(Request $request)
    {
        set_time_limit(0);

        $from = $request->input('from_date');
        $to = $request->input('to_date');

        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date' => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        try {

            $MqrDailyRidership = [];

            $stations = DB::table('station_inventory')
                ->select('stn_short_name', 'stn_id')
                ->orderBy('stn_id', 'ASC')
                ->get();

            foreach ($stations as $station) {

                $data = [
                    'stn_name' => $station->stn_short_name,
                    'stn_code' => $station->stn_id,
                ];

                /* SJT VALIDATION */
                $sjtValidation = DB::table('msjt_validation')
                    ->where('pass_id', 10)
                    ->where('is_test', false)
                    ->whereBetween('txn_date', [$from, $to])
                    ->where('stn_id', $station->stn_id)
                    ->where('val_type_id', 1)
                    ->count();

                /* RJT VALIDATION */
                $rjtValidation = DB::table('mrjt_validation')
                    ->where('pass_id', 90)
                    ->where('is_test', false)
                    ->whereBetween('txn_date', [$from, $to])
                    ->where('stn_id', $station->stn_id)
                    ->where('val_type_id', 1)
                    ->count();

                /* 45 TRIP PASS */
                /*$normalTripValidation = DB::table('mtp_validation')
                    ->where('pass_id', 21)
                    ->where('is_test', false)
                    ->whereBetween('txn_date', [$from, $to])
                    ->where('stn_id', $station->stn_id)
                    ->where('val_type_id', 1)
                    ->count();*/

                /* STORE VALUE PASS */
                /*$storeValueValidation = DB::table('msv_validation')
                    ->where('pass_id', 81)
                    ->where('is_test', false)
                    ->whereBetween('txn_date', [$from, $to])
                    ->where('stn_id', $station->stn_id)
                    ->where('val_type_id', 1)
                    ->count();*/

                $data["SJT"] = $sjtValidation;
                $data["RJT"] = $rjtValidation;
                $data["45T"] = 0;
                $data["ULT"] = 0;
                $data["SVP"] = 0;

                $MqrDailyRidership[] = $data;
            }

            return response()->json([
                'status' => true,
                'data' => $MqrDailyRidership,
            ]);

        } catch (PDOException $e) {
            return response([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }

    }
}
