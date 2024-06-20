<?php

namespace App\Http\Controllers\Modules\ReportApi\MQR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MqrAccReport extends Controller
{
    /* SJT ACCOUNTING REPORT */
    public function sjtAccReport(Request $request)
    {
        set_time_limit(0);

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

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $sjtAccounting = DB::table('msjt_ms_accounting')
                ->leftJoin('msjt_sl_accounting', 'msjt_ms_accounting.ms_qr_no', '=', 'msjt_sl_accounting.ms_qr_no')
                ->whereBetween('msjt_ms_accounting.txn_date', [$fromDate, $toDate])
                ->orderBy('msjt_ms_accounting.txn_date', 'ASC')
                ->where('msjt_ms_accounting.is_test','=',false)
                ->get([
                'msjt_ms_accounting.app_order_id as order_id',
                'msjt_ms_accounting.txn_date',
                'msjt_ms_accounting.ms_qr_no',
                'msjt_ms_accounting.op_type_id',
                'msjt_ms_accounting.stn_id',
                'msjt_ms_accounting.src_stn_id',
                'msjt_ms_accounting.des_stn_id',
                'msjt_ms_accounting.units',
                'msjt_ms_accounting.unit_price',
                'msjt_ms_accounting.total_price',
                'msjt_ms_accounting.pax_first_name as first_name',
                'msjt_ms_accounting.pax_last_name as last_name',
                'msjt_ms_accounting.pax_mobile as mobile_no',
                'msjt_ms_accounting.media_type_id',
                'msjt_ms_accounting.product_id',
                'msjt_ms_accounting.pass_id',
                'msjt_ms_accounting.ms_qr_exp',
                'msjt_ms_accounting.travel_date',
                'msjt_ms_accounting.processing_fee as process_fee',
                'msjt_ms_accounting.pay_type_id',
                'msjt_ms_accounting.pay_ref',
                'msjt_ms_accounting.app_id',
                'msjt_ms_accounting.app_cust_id',
                'msjt_ms_accounting.pg_id',
                'msjt_ms_accounting.pg_order_id',
                'msjt_ms_accounting.created_at',
                'msjt_sl_accounting.sl_acc_id',
                'msjt_sl_accounting.sl_qr_no',
                'msjt_sl_accounting.sl_qr_exp',
                'msjt_sl_accounting.ref_qr_no',
                'msjt_sl_accounting.qr_dir',
                'msjt_sl_accounting.qr_status',
                ]);


            if ($sjtAccounting->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404); // 404 Not Found
            }

            $dateWiseData = [];
            /******
             * STORING DATE IN STRING TYPE ONLY SO THAT WE CAN SHOW IN RESPONSE DATE WISE
             ******/
            foreach ($sjtAccounting as $transaction) {
                $date = substr($transaction->txn_date, 0, 10);
                $dateWiseData[$date][] = $transaction;
            }

            return response([
                'status' => true,
                'data' => $dateWiseData
            ]);

        } catch (PDOException $e) {
            Log::channel('mqrAccounting')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            Log::channel('mqrAccounting')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }

    }

    /* RJT ACCOUNTING REPORT */
    public function rjtAccReport(Request $request)
    {
        set_time_limit(0);

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

            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');

            $rjtAccounting = DB::table('mrjt_ms_accounting')
                ->leftJoin('mrjt_sl_accounting', 'mrjt_ms_accounting.ms_qr_no', '=', 'mrjt_sl_accounting.ms_qr_no')
                ->whereBetween('mrjt_ms_accounting.txn_date', [$fromDate, $toDate])
                ->orderBy('mrjt_ms_accounting.txn_date', 'ASC')
                ->where('mrjt_ms_accounting.rs_test','=',false)
                ->get([
                    'mrjt_ms_accounting.app_order_id as order_id',
                    'mrjt_ms_accounting.txn_date',
                    'mrjt_ms_accounting.ms_qr_no',
                    'mrjt_ms_accounting.op_type_id',
                    'mrjt_ms_accounting.stn_id',
                    'mrjt_ms_accounting.src_stn_id',
                    'mrjt_ms_accounting.des_stn_id',
                    'mrjt_ms_accounting.units',
                    'mrjt_ms_accounting.unit_price',
                    'mrjt_ms_accounting.total_price',
                    'mrjt_ms_accounting.pax_first_name as first_name',
                    'mrjt_ms_accounting.pax_last_name as last_name',
                    'mrjt_ms_accounting.pax_mobile as mobile_no',
                    'mrjt_ms_accounting.media_type_id',
                    'mrjt_ms_accounting.product_id',
                    'mrjt_ms_accounting.pass_id',
                    'mrjt_ms_accounting.ms_qr_exp',
                    'mrjt_ms_accounting.travel_date',
                    'mrjt_ms_accounting.processing_fee as process_fee',
                    'mrjt_ms_accounting.pay_type_id',
                    'mrjt_ms_accounting.pay_ref',
                    'mrjt_ms_accounting.app_id',
                    'mrjt_ms_accounting.app_cust_id',
                    'mrjt_ms_accounting.pg_id',
                    'mrjt_ms_accounting.pg_order_id',
                    'mrjt_ms_accounting.created_at',
                    'mrjt_sl_accounting.sl_acc_id',
                    'mrjt_sl_accounting.sl_qr_no',
                    'mrjt_sl_accounting.sl_qr_exp',
                    'mrjt_sl_accounting.ref_qr_no',
                    'mrjt_sl_accounting.qr_dir',
                    'mrjt_sl_accounting.qr_status',
                ]);


            if ($rjtAccounting->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404);
            }

            $dateWiseData = [];
            /******
             * STORING DATE IN STRING TYPE ONLY SO THAT WE CAN SHOW IN RESPONSE DATE WISE
             ******/
            foreach ($rjtAccounting as $transaction) {
                $date = substr($transaction->txn_date, 0, 10);
                $dateWiseData[$date][] = $transaction;
            }

            return response([
                'status' => true,
                'data' => $dateWiseData
            ]);

        } catch (PDOException $e) {
            /* IF COLUMN IDENTITY FOUND AS ERROR */
            if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                $transData['is_settled'] = true;
            } else {
                $transData['is_settled'] = false;
            }
            $transData['order_id']  = $transaction->order_id;
            $transData['error']     = $e->getMessage();
            Log::channel('mqrAccounting')->info($e->getMessage());

            return $transData;
        }

    }

}
