<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;


class ClTravelApi extends Controller
{
    public function svValReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date'   => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        try {

            $data = DB::table('cl_sv_validation')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->whereIn('pass_id', [23, 63, 73, 83, 84])
                ->get([
                    'atek_id as order_id',
                    'txn_date',
                    'engraved_id',
                    'val_type_id',
                    'amt_deducted',
                    'chip_balance as card_bal',
                    'media_type_id',
                    'product_id',
                    'pass_id',
                    'eq_id',
                    'stn_id',
                    'is_test'
                ]);

            if ($data->isEmpty()) {
                return response([
                    'status' => false,
                    'error'  => 'No records found!'
                ], 404);
            }

            return response([
                'status' => true,
                'data' => $data
            ]);

        } catch (PDOException $e) {
            return response([
                'status'=> false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function tpValReport(Request $request)
    {
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

            $data = DB::table('cl_tp_validation')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->whereIn('pass_id', [23, 63, 73, 83, 24, 64])
                ->get([
                    'atek_id as order_id',
                    'txn_date',
                    'engraved_id',
                    'val_type_id',
                    'trip_deducted as amt_deducted',
                    'trip_balance as card_bal',
                    'media_type_id',
                    'product_id',
                    'pass_id',
                    'eq_id',
                    'stn_id',
                    'is_test',
                ]);

            if ($data->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404);
            }

            return response([
                'status' => true,
                'data' => $data
            ]);

        } catch (PDOException $e) {
            return response([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
