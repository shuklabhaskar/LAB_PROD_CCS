<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class DailyRidershipReport extends Controller
{
    public function dailyRidership(Request $request)
    {
        /* VALIDATING REQUEST */
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date' => 'required|date_format:Y-m-d H:i:s',
        ]);

        /* IF DATE FORMAT OR DATE IS WRONG */
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()
            ]);
        }

        $fromDate   = $request->input('from_date');
        $toDate     = $request->input('to_date');

        /* GETTING DATA FROM BOTH CL_TP_VALIDATION AND CL_SV_VALIDATION */
        $passes = DB::table('cl_tp_validation')
            ->join('pass_inventory', 'pass_inventory.pass_id', '=', 'cl_tp_validation.pass_id')
            ->join('station_inventory', 'station_inventory.stn_id', '=', 'cl_tp_validation.stn_id')
            ->whereBetween(DB::raw('(txn_date)'), [$fromDate, $toDate])
            ->where('cl_tp_validation.val_type_id', '=', 2)
            ->select('pass_inventory.pass_name', 'pass_inventory.pass_id', 'station_inventory.stn_name')
            ->unionAll(
                DB::table('cl_sv_validation')
                    ->join('pass_inventory', 'pass_inventory.pass_id', '=', 'cl_sv_validation.pass_id')
                    ->join('station_inventory', 'station_inventory.stn_id', '=', 'cl_sv_validation.stn_id')
                    ->whereBetween(DB::raw('(txn_date)'), [$fromDate, $toDate])
                    ->where('cl_sv_validation.val_type_id', '=', 2)
                    ->select('pass_inventory.pass_name', 'pass_inventory.pass_id', 'station_inventory.stn_name')
            )
            ->get();

        $svAndTpData = [];

        foreach ($passes as $pass) {

            $pass_id = $pass->pass_id;

            if (!isset($svAndTpData[$pass_id])) {
                $svAndTpData[$pass_id] = [
                    'pass_name'     => $pass->pass_name,
                    'pass_id'       => $pass_id,
                    'total'         => 0,
                    'stations'      => [],
                ];
            }

            $svAndTpData[$pass_id]['total']++;

            $station_name = $pass->stn_name;
            if (!isset($svAndTpData[$pass_id]['stations'][$station_name])) {
                $svAndTpData[$pass_id]['stations'][$station_name] = 0;
            }

            // INCREMENTING STATION COUNT FOR BOTH CL_TP_VALIDATION AND CL_SV_VALIDATION
            $svAndTpData[$pass_id]['stations'][$station_name]++;
        }

        $dailyRiderShipData = array_values($svAndTpData);

        if (empty($dailyRiderShipData)) {
            return response([
                "status" => false,
                "error" => "No Such Data is Available for This Date Range",
            ]);
        }

        return response([
            "status"    => true,
            "data"      => $dailyRiderShipData,
        ]);
    }






}


/*MAKE SURE FOR IS TEST IN QUERY*/
