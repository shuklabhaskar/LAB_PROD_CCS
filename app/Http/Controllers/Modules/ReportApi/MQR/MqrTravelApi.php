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
                ], 404);
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
        } catch (Exception $e) {
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
                ], 404);
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
        } catch (Exception $e) {
            Log::channel('mqrValidation')->error($e->getMessage(), ['code' => $e->getCode()]);
            return response()->json([
                'status' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

}
