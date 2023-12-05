<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClFicoReport extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date_format:Y-m-d',
            'to'   => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()
            ]);
        }

        $ficoReport = DB::table('mmopl_cl_sap_fico_posting')
            ->whereBetween('record_date', [$request->input('from'), $request->input('to')])
            ->select([
                'record_date',
                'pass_exit_revenue',
                'pass_stale_revenue',
                'purse_exit_revenue',
                'purse_stale_revenue',
            ])
            ->get();

        if ($ficoReport->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No data available for the specified date range',
            ]);
        }


        return response()->json([
            'status' => true,
            'data'   => $ficoReport
        ]);

    }


}
