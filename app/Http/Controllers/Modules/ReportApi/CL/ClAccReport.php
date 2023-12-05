<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;


class ClAccReport extends Controller
{
    public function svAccReport(Request $request)
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
            $svAccounting = DB::table('cl_sv_accounting')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->whereIn('pass_id', [23, 63, 73, 83])
                ->orderBy('txn_date','ASC')
                ->get([
                    'txn_date',
                    'engraved_id',
                    'pass_id',
                    'op_type_id',
                    'pay_type_id',
                    'pass_price',
                    'card_sec as deposit',
                    'pax_first_name as name',
                    'pax_mobile as mobile_no',
                    'old_engraved_id',
                    'user_id',
                    'shift_id',
                    'eq_id'
                ]);

            if ($svAccounting->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404);
            }

            $dateWiseData = [];
            /******
             STORING DATE IN STRING TYPE ONLY SO THAT WE CAN SHOW IN RESPONSE DATE WISE
             ******/
            foreach ($svAccounting as $transaction) {
                $date = substr($transaction->txn_date, 0, 10);
                $dateWiseData[$date][] = $transaction;
            }

            return response([
                'status' => true,
                'data' => $dateWiseData
            ]);

        } catch (PDOException $e) {
            return response([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function tpAccReport(Request $request)
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
            $svAccounting = DB::table('cl_tp_accounting')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->whereIn('pass_id', [23, 63, 73, 83])
                ->orderBy('txn_date','ASC')
                ->get([
                    'txn_date',
                    'engraved_id',
                    'pass_id',
                    'op_type_id',
                    'pay_type_id',
                    'num_trips',
                    'card_sec as deposit',
                    'pass_price',
                    'pax_first_name as name',
                    'pax_mobile as mobile_no',
                    'old_engraved_id',
                    'user_id',
                    'shift_id',
                    'eq_id'
                ]);

            if ($svAccounting->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404);
            }

            $dateWiseData = [];
            /******
            STORING DATE IN STRING TYPE ONLY SO THAT WE CAN SHOW IN RESPONSE DATE WISE
             ******/
            foreach ($svAccounting as $transaction) {
                $date = substr($transaction->txn_date, 0, 10);
                $dateWiseData[$date][] = $transaction;
            }

            return response([
                'status' => true,
                'data' => $dateWiseData
            ]);

        } catch (PDOException $e) {
            return response([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }


    }
}
