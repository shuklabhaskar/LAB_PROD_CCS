<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DailyRidershipReport extends Controller
{
    function dailyRidership(Request $request)
    {

        $from = $request->from_date;
        $to = $request->to_date;

        $count = [];

        /* VALIDATION */
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date' => 'required|date_format:Y-m-d H:i:s',
        ]);

        /* IF VALIDATION FAILS */
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        } else {

            /* CONVERTING DATE TO LARAVEL DATE */
            $convertedFrom = Carbon::make($from);
            $convertedTo = Carbon::make($to);

            $passes = DB::table('pass_inventory')
                ->where('media_type_id', 2)
                ->get([
                    'pass_name',
                    'pass_id'
                ]);

            foreach ($passes as $pass) {

                $data = DB::table('cl_tp_validation')
                    ->join('station_inventory', 'station_inventory.stn_id', '=', 'cl_tp_validation.stn_id')
                    ->where('pass_id', '=', $pass->pass_id)
                    ->whereBetween(DB::raw('(txn_date)'), [$convertedFrom, $convertedTo])
                    ->get();

                $data['Pass'] = $data;

                $count[] = $data['Pass'];

            }


        }

        return response([
            'status' => true,
            'data' => $count
        ]);
    }
}
