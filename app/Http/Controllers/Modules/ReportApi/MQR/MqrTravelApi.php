<?php

namespace App\Http\Controllers\Modules\ReportApi\MQR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;

class MqrTravelApi extends Controller
{

    public function sjtValReport(Request $request)
    {
        set_time_limit(0);

        $request->validate([
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date'   => 'required|date_format:Y-m-d H:i:s',
        ]);

        try {

            $data = DB::table('msjt_validation')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->where('is_test', '=', false)
                ->get([
                    'sl_qr_no',
                    'pass_id',
                    DB::raw("TO_CHAR(txn_date, 'YYYY-MM-DD HH24:MI:SS') as txn_date"),
                    'stn_id',
                    'eq_id',
                    'val_type_id',
                ]);


            if ($data->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 500);
            }

            return response([
                'status' => true,
                'data' => $data
            ]);

        } catch (PDOException $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rjtValReport(Request $request)
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

            $data = DB::table('mrjt_validation')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->where('is_test', '=', false)
                ->get([
                    'sl_qr_no',
                    'pass_id',
                    DB::raw("TO_CHAR(txn_date, 'YYYY-MM-DD HH24:MI:SS') as txn_date"),
                    'stn_id',
                    'eq_id',
                    'val_type_id',
                ]);

            if ($data->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 500);
            }

            return response([
                'status' => true,
                'data' => $data
            ]);

        } catch (PDOException $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function svValReport(Request $request)
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

            $data = DB::table('msv_validation')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->where('is_test', '=', false)
                ->get([
                    'sl_qr_no',
                    'pass_id',
                    DB::raw("TO_CHAR(txn_date, 'YYYY-MM-DD HH24:MI:SS') as txn_date"),
                    'stn_id',
                    'eq_id',
                    'val_type_id',
                ]);

            if ($data->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 500);
            }

            return response([
                'status' => true,
                'data' => $data
            ]);

        } catch (PDOException $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tpValReport(Request $request)
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

            $data = DB::table('mtp_validation')
                ->whereBetween('txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->where('is_test', '=', false)
                ->get([
                    'sl_qr_no',
                    'pass_id',
                    DB::raw("TO_CHAR(txn_date, 'YYYY-MM-DD HH24:MI:SS') as txn_date"),
                    'stn_id',
                    'eq_id',
                    'val_type_id',
                ]);

            if ($data->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 500);
            }

            return response([
                'status' => true,
                'data' => $data
            ]);

        } catch (PDOException $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }



    /* FOR DUMP DATA ONLY */
    public function svVal2Report(Request $request)
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

            $data = DB::table('msv_validation')
                ->join('msv_sl_accounting','msv_sl_accounting.sl_qr_no','=','msv_validation.sl_qr_no')
                ->whereBetween('msv_validation.txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->where('is_test', '=', false)
                ->get([
                    'msv_validation.ms_qr_no',
                    'msv_validation.sl_qr_no',
                    'msv_validation.pass_id',
                    DB::raw("TO_CHAR(msv_validation.txn_date, 'YYYY-MM-DD HH24:MI:SS') as txn_date"),
                    'msv_validation.stn_id',
                    'msv_validation.eq_id',
                    'msv_validation.val_type_id',
                    'msv_sl_accounting.amt_deducted',
                    'msv_sl_accounting.post_bal_amt as post_bal_amt',
                    'msv_sl_accounting.pre_bal_amt as pre_bal_amt'
                ]);

            if ($data->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 500);
            }

            return response([
                'status' => true,
                'data' => $data
            ]);

        } catch (PDOException $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tpVal2Report(Request $request)
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

            $data = DB::table('mtp_validation')
                ->join('mtp_sl_accounting','mtp_sl_accounting.sl_qr_no','=','mtp_validation.sl_qr_no')
                ->whereBetween('mtp_validation.txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->where('is_test', '=', false)
                ->get([
                    'mtp_validation.ms_qr_no',
                    'mtp_validation.sl_qr_no',
                    'mtp_validation.pass_id',
                    DB::raw("TO_CHAR(mtp_validation.txn_date, 'YYYY-MM-DD HH24:MI:SS') as txn_date"),
                    'mtp_validation.stn_id',
                    'mtp_validation.eq_id',
                    'mtp_validation.val_type_id',
                    'mtp_sl_accounting.trip_deducted',
                    'mtp_sl_accounting.bal_trips as balance_trips'
                ]);

            if ($data->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 500);
            }

            return response([
                'status' => true,
                'data' => $data
            ]);

        } catch (PDOException $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }


}
