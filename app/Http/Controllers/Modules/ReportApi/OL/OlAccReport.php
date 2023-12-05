<?php

namespace App\Http\Controllers\Modules\ReportApi\OL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class OlAccReport extends Controller
{

    public function olSaleReport(Request $request){

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
            $svAccounting = DB::table('ol_card_sale')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->orderBy('txn_date','ASC')
                ->get([
                    'card_hash_no',
                    'txn_date',
                    'pax_mobile as mobile_number',
                    'op_type_id',
                    'card_sec',
                    'total_price as pass_price',
                    'eq_id',
                    'user_id',
                    'shift_id',
                    'stn_id',

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

    public function olSvAccReport(Request $request){
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
            $svAccounting = DB::table('ol_sv_accounting')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->orderBy('txn_date','ASC')
                ->get([
                  'card_hash_no',
                  'txn_date',
                  'pass_id',
                  'op_type_id',
                  'pay_type_id',
                  'total_price as pass_price',
                  'user_id',
                  'shift_id',
                  'eq_id',
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
