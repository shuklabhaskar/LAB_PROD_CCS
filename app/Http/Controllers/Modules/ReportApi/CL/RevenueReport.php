<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RevenueReport extends Controller
{
    public function revenue(Request $request)
    {
        // VALIDATING THE REQUEST
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

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        // Initialize an array to store the revenue report data
        $revenueReport = [];

        // Query to get passes and related data
        $passes = DB::table('cl_tp_accounting')
            ->join('station_inventory', 'station_inventory.stn_id', '=', 'cl_tp_accounting.stn_id')
            ->join('pass_inventory', 'pass_inventory.pass_id', '=', 'cl_tp_accounting.pass_id')
            ->whereBetween(DB::raw('(txn_date)'), [$fromDate, $toDate])
            ->select('pass_inventory.pass_name', 'pass_inventory.pass_id', 'station_inventory.stn_name', 'station_inventory.stn_id')
            ->unionAll(
                DB::table('cl_sv_accounting')
                    ->join('station_inventory', 'station_inventory.stn_id', '=', 'cl_sv_accounting.stn_id')
                    ->join('pass_inventory', 'pass_inventory.pass_id', '=', 'cl_sv_accounting.pass_id')
                    ->whereBetween(DB::raw('(txn_date)'), [$fromDate, $toDate])
                    ->select('pass_inventory.pass_name', 'pass_inventory.pass_id', 'station_inventory.stn_name', 'station_inventory.stn_id')
            )
            ->get();

        foreach ($passes as $pass) {
            $pass_id = $pass->pass_id;

            // Initialize or get the pass entry in the report array
            if (!isset($revenueReport[$pass_id])) {
                $revenueReport[$pass_id] = [
                    'pass_name'         => $pass->pass_name,
                    'pass_id'           => $pass_id,
                    'totalIssuedCount'  => 0,
                    'totalReloadCount'  => 0,
                    'totalRefundCount'  => 0,
                    'stations'          => [],
                ];
            }

            $station_id = $pass->stn_id;
            $station_name = strtolower($pass->stn_name);

            // Initialize or get the station entry for this pass
            if (!isset($revenueReport[$pass_id]['stations'][$station_name])) {
                $revenueReport[$pass_id]['stations'][$station_name] = [
                    'issued_count' => 0,
                    'reload_count' => 0,
                    'refund_count' => 0,
                ];
            }

            // Query to get counts for issued, reload, and refund
            $counts = DB::table('cl_sv_accounting')
                ->select(
                    DB::raw('SUM(CASE WHEN op_type_id = 11 THEN 1 ELSE 0 END) as issued_count'),
                    DB::raw('SUM(CASE WHEN op_type_id = 3 THEN 1 ELSE 0 END) as reload_count'),
                    DB::raw('SUM(CASE WHEN op_type_id = 6 THEN 1 ELSE 0 END) as refund_count')
                )
                ->whereBetween(DB::raw('(txn_date)'), [$fromDate, $toDate])
                ->where('stn_id', '=', $station_id)
                ->first();

            // Update the counts for this station
            $revenueReport[$pass_id]['stations'][$station_name]['issued_count'] += $counts->issued_count;
            $revenueReport[$pass_id]['stations'][$station_name]['reload_count'] += $counts->reload_count;
            $revenueReport[$pass_id]['stations'][$station_name]['refund_count'] += $counts->refund_count;

            // Update the totalIssuedCount, totalReloadCount, and totalRefundCount for this pass
            $revenueReport[$pass_id]['totalIssuedCount'] += $counts->issued_count;
            $revenueReport[$pass_id]['totalReloadCount'] += $counts->reload_count;
            $revenueReport[$pass_id]['totalRefundCount'] += $counts->refund_count;
        }

        // Convert the associative array to indexed array for JSON response
        $revenueData = array_values($revenueReport);

        return response([
            'status' => true,
            'data' => $revenueData,
        ]);
    }
}
