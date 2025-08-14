<?php

namespace App\Http\Controllers\Modules\ReportApi\MQR;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;

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

            /* FOR SJT TRANSACTION */
            $sjtAccounting = DB::table('msjt_ms_accounting')
                ->leftJoin('msjt_sl_accounting', 'msjt_ms_accounting.ms_qr_no', '=', 'msjt_sl_accounting.ms_qr_no')
                ->whereBetween('msjt_ms_accounting.txn_date', [$fromDate, $toDate])
                ->where('msjt_ms_accounting.is_test', false)
                ->select([
                    'msjt_ms_accounting.txn_date',
                    'msjt_ms_accounting.app_id',
                    'msjt_sl_accounting.sl_qr_no',
                    'msjt_ms_accounting.pass_id',
                    'msjt_ms_accounting.pax_mobile as mobile_no',
                    'msjt_ms_accounting.pax_first_name',
                    'msjt_ms_accounting.pax_last_name',
                    'msjt_ms_accounting.op_type_id',
                    'msjt_ms_accounting.unit_price',
                    'msjt_ms_accounting.total_price',
                ])->get();

            if ($sjtAccounting->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404);
            }

            $dateWiseData = [];

            // Grouping data by date
            foreach ($sjtAccounting as $transaction) {
                $date = substr($transaction->txn_date, 0, 10);
                $dateWiseData[$date][] = $transaction;
            }

            return response()->json([
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
                ->where('mrjt_ms_accounting.is_test', false)
                ->select([
                    'mrjt_ms_accounting.txn_date',
                    'mrjt_ms_accounting.app_id',
                    'mrjt_sl_accounting.sl_qr_no',
                    'mrjt_ms_accounting.pass_id',
                    'mrjt_ms_accounting.pax_mobile as mobile_no',
                    'mrjt_ms_accounting.pax_first_name',
                    'mrjt_ms_accounting.pax_last_name',
                    'mrjt_ms_accounting.op_type_id',
                    'mrjt_ms_accounting.unit_price',
                    'mrjt_ms_accounting.total_price',
                ])->get();

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

    /* SV ACCOUNTING REPORT */
    public function svAccReport(Request $request){
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

            $svAccounting = DB::table('msv_ms_accounting')
                ->whereBetween('msv_ms_accounting.txn_date', [$fromDate, $toDate])
                ->where('msv_ms_accounting.is_test', false)
                ->select([
                    'msv_ms_accounting.txn_date',
                    'msv_ms_accounting.app_id',
                    'msv_ms_accounting.ms_qr_no',
                    'msv_ms_accounting.pass_id',
                    'msv_ms_accounting.pax_mobile as mobile_no',
                    'msv_ms_accounting.pax_first_name',
                    'msv_ms_accounting.pax_last_name',
                    'msv_ms_accounting.op_type_id',
                    'msv_ms_accounting.pass_price',
                    'msv_ms_accounting.total_price as recharge_amount',
                    'msv_ms_accounting.ms_qr_exp as expiry_date',
                ])->get();

            if ($svAccounting->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404);
            }

            $dateWiseData = [];
            /******
             * STORING DATE IN STRING TYPE ONLY SO THAT WE CAN SHOW IN RESPONSE DATE WISE
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

    /* TP ACCOUNTING REPORT */
    public function tptAccReport(Request $request)
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

            $tpAccounting = DB::table('mtp_ms_accounting')
                ->whereBetween('mtp_ms_accounting.txn_date', [$fromDate, $toDate])
                ->where('mtp_ms_accounting.is_test', false)
                ->select([
                    'mtp_ms_accounting.txn_date',
                    'mtp_ms_accounting.app_id',
                    'mtp_ms_accounting.ms_qr_no',
                    'mtp_ms_accounting.pass_id',
                    'mtp_ms_accounting.pax_mobile as mobile_no',
                    'mtp_ms_accounting.pax_first_name',
                    'mtp_ms_accounting.pax_last_name',
                    'mtp_ms_accounting.op_type_id',
                    'mtp_ms_accounting.pass_price',
                    'mtp_ms_accounting.total_price as recharge_amount',
                    'mtp_ms_accounting.rem_trips as trip_count',
                    'mtp_ms_accounting.ms_qr_exp as expiry_date',
                ])->get();

            if ($tpAccounting->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404);
            }

            $dateWiseData = [];
            /******
             * STORING DATE IN STRING TYPE ONLY SO THAT WE CAN SHOW IN RESPONSE DATE WISE
             ******/
            foreach ($tpAccounting as $transaction) {
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

}
