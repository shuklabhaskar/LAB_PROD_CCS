<?php

namespace App\Http\Controllers\Modules\ReportApi\OL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class OlFicoReport extends Controller
{
    public function index(Request $request)
    {
        // VALIDATION
        $validator = Validator::make($request->all(), [
            'from' => 'required|date_format:Y-m-d',
            'to' => 'required|date_format:Y-m-d',
        ]);

        // IF VALIDATION FAILS
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }

        // VALIDATED REQUEST VALUES
        $from = Carbon::parse($request->from)->toDateString();
        $to = Carbon::parse($request->to)->toDateString();

        // FETCH DATA FROM DATABASE
        $ficoReport = DB::table('mmopl_ol_sap_fico_posting')
            ->whereBetween('record_date', [$from, $to])
            ->select([
                'record_date',
                'pass_exit_revenue',
                'pass_stale_revenue',
                'purse_exit_revenue',
                'purse_stale_revenue',
                'penalty_revenue',
            ])
            ->get();

        // CHECK IF DATA IS EMPTY
        if ($ficoReport->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No data available for the specified date range',
            ]);
        }

        // RETURN SUCCESSFUL RESPONSE WITH DATA
        return response()->json([
            'status' => true,
            'data' => $ficoReport
        ]);
    }

}
