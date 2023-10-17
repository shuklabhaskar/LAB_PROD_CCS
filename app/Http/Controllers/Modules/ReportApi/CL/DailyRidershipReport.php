<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class DailyRidershipReport extends Controller
{
    public function dailyRidership(Request $request)
    {
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

            $clDailyRidership = [];

            /* TO GET NUMBER OF STATION AND STATION NAME */
            $stations = DB::table('station_inventory')
                ->select('stn_short_name', 'stn_id')
                ->orderBy('stn_id', 'ASC')
                ->get();

            $Passes = DB::table('pass_inventory')
                ->whereNot('pass_id', '=',53)
                ->where('status','=', true)
                ->where('is_test', '=',false)
                ->where('media_type_id', '=', 2)
                ->select(['pass_name', 'pass_id'])
                ->get();

            foreach ($stations as $station) {

                $data = [
                    'stn_name' => $station->stn_short_name,
                    'stn_code' => $station->stn_id,
                ];

                foreach ($Passes as $pass) {

                    $svValidation = DB::table('cl_sv_validation')
                        ->where('pass_id', '=',$pass->pass_id)
                        ->whereBetween(DB::raw('(cl_sv_validation.txn_date)'), [$from, $to])
                        ->where('stn_id', '=',$station->stn_id)
                        ->whereNot('pass_id', '=',53)
                        ->where('val_type_id', '=',1)
                        ->count();

                    $tpValidation = DB::table('cl_tp_validation')
                        ->where('pass_id', '=',$pass->pass_id)
                        ->whereBetween(DB::raw('(cl_tp_validation.txn_date)'), [$from, $to])
                        ->where('stn_id','=', $station->stn_id)
                        ->whereNot('pass_id','=',53)
                        ->where('val_type_id','=',1)
                        ->count();

                    $data[$pass->pass_name] = $svValidation + $tpValidation;
                    $clDailyRidership[] = $data;
                }


            }

            return response([
                'status' => true,
                'data' => $clDailyRidership,
            ]);
        }
    }
}
