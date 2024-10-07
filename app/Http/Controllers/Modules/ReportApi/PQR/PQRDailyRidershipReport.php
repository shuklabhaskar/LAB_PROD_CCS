<?php

namespace App\Http\Controllers\Modules\ReportApi\PQR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class PQRDailyRidershipReport extends Controller
{
    public function dailyRidership(Request $request)
    {
        set_time_limit(0);

        $from = $request->input('from_date');
        $to   = $request->input('to_date');

        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date'   => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors(),
            ]);
        }

        try {

            $PqrDailyRidership = [];

            $stations = DB::table('station_inventory')
                ->select('stn_short_name', 'stn_id')
                ->orderBy('stn_id', 'ASC')
                ->get();

            foreach ($stations as $station) {

                $data = [
                    'stn_name' => $station->stn_short_name,
                    'stn_code' => $station->stn_id,
                ];


                /***
                 * BELOW PASS ID FOR RESPECTIVE PRODUCT TYPE
                 * SJT = 15
                 * RJT = 95
                 */

                /* SJT VALIDATION */
                $sjtValidation = DB::table('psjt_validation')
                    ->where('pass_id', 15)
                    ->where('is_test', false)
                    ->whereBetween('txn_date', [$from, $to])
                    ->where('stn_id', $station->stn_id)
                    ->where('val_type_id', 1)
                    ->count();

                /* RJT VALIDATION */
                $rjtValidation = DB::table('prjt_validation')
                    ->where('pass_id', 95)
                    ->where('is_test', false)
                    ->whereBetween('txn_date', [$from, $to])
                    ->where('stn_id', $station->stn_id)
                    ->where('val_type_id', 1)
                    ->count();

                $data["SJT"] = $sjtValidation;
                $data["RJT"] = $rjtValidation;

                $PqrDailyRidership[] = $data;
            }

            return response()->json([
                'status' => true,
                'data'   => $PqrDailyRidership,
            ]);

        } catch (PDOException $e) {
            return response([
                'status' => false,
                'error'  => $e->getMessage()
            ], 500);
        }

    }
}
