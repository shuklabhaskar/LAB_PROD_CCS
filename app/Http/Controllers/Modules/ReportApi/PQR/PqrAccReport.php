<?php

namespace App\Http\Controllers\Modules\ReportApi\PQR;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;

class PqrAccReport extends Controller
{
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
            $sjtAccounting = DB::table('psjt_ms_accounting')
                ->leftJoin('psjt_sl_accounting', 'psjt_sl_accounting.ms_qr_no', '=', 'psjt_ms_accounting.ms_qr_no')
                ->whereBetween('psjt_ms_accounting.txn_date', [$fromDate, $toDate])
                ->where('psjt_ms_accounting.is_test', false)
                ->select([
                    'psjt_ms_accounting.txn_date',
                    'psjt_sl_accounting.sl_qr_no',
                    'psjt_ms_accounting.pass_id',
                    'psjt_ms_accounting.pax_mobile as mobile_no',
                    'psjt_ms_accounting.pax_first_name',
                    'psjt_ms_accounting.pax_last_name',
                    'psjt_ms_accounting.op_type_id',
                    'psjt_ms_accounting.unit_price',
                    'psjt_ms_accounting.total_price',
                    'psjt_ms_accounting.stn_id',
                    'psjt_ms_accounting.eq_id',
                    'psjt_ms_accounting.pay_type_id',
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
            Log::channel('pqrAccounting')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            Log::channel('pqrAccounting')->error($e->getMessage(), ['code' => $e->getCode()]);
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

            $rjtAccounting = DB::table('prjt_ms_accounting')
                ->leftJoin('prjt_sl_accounting', 'prjt_ms_accounting.ms_qr_no', '=', 'prjt_sl_accounting.ms_qr_no')
                ->whereBetween('prjt_ms_accounting.txn_date', [$fromDate, $toDate])
                ->where('prjt_ms_accounting.is_test', false)
                ->select([
                    'prjt_ms_accounting.txn_date',
                    'prjt_sl_accounting.sl_qr_no',
                    'prjt_ms_accounting.pass_id',
                    'prjt_ms_accounting.pax_mobile as mobile_no',
                    'prjt_ms_accounting.pax_first_name',
                    'prjt_ms_accounting.pax_last_name',
                    'prjt_ms_accounting.op_type_id',
                    'prjt_ms_accounting.unit_price',
                    'prjt_ms_accounting.total_price',
                    'prjt_ms_accounting.stn_id',
                    'prjt_ms_accounting.eq_id',
                    'prjt_ms_accounting.pay_type_id',
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
            Log::channel('pqrAccounting')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (Exception $e) {
            Log::channel('pqrAccounting')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }

    }

}
