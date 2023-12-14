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
                ->leftJoin('cl_status', function ($join) {
                    $join->on('cl_sv_accounting.engraved_id', '=', 'cl_status.engraved_id');
                })
                ->whereBetween('cl_sv_accounting.txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->whereIn('cl_sv_accounting.pass_id', [23, 63, 73, 83])
                ->orderBy('cl_sv_accounting.txn_date', 'ASC')
                ->get([
                    'cl_sv_accounting.txn_date',
                    'cl_sv_accounting.engraved_id',
                    'cl_sv_accounting.pass_id',
                    'cl_sv_accounting.op_type_id',
                    'cl_sv_accounting.pay_type_id',
                    'cl_sv_accounting.pass_price',
                    'cl_sv_accounting.card_sec as deposit',
                    DB::raw("cl_status.pax_first_name"),
                    DB::raw("cl_status.pax_mobile"),
                    'cl_sv_accounting.old_engraved_id',
                    'cl_sv_accounting.user_id',
                    'cl_sv_accounting.shift_id',
                    'cl_sv_accounting.eq_id'
                ]);

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

            $tpAccounting = DB::table('cl_tp_accounting')
                ->leftJoin('cl_status', function ($join) {
                    $join->on('cl_tp_accounting.engraved_id', '=', 'cl_status.engraved_id');
                })
                ->whereBetween('cl_tp_accounting.txn_date', [$request->input('from_date'), $request->input('to_date')])
                ->whereIn('cl_tp_accounting.pass_id', [23, 63, 73, 83])
                ->orderBy('cl_tp_accounting.txn_date', 'ASC')
                ->get([
                    'cl_tp_accounting.txn_date',
                    'cl_tp_accounting.engraved_id',
                    'cl_tp_accounting.pass_id',
                    'cl_tp_accounting.op_type_id',
                    'cl_tp_accounting.pay_type_id',
                    'cl_tp_accounting.num_trips',
                    'cl_tp_accounting.card_sec as deposit',
                    'cl_tp_accounting.pass_price',
                    DB::raw("cl_status.pax_first_name"),
                    DB::raw("cl_status.pax_mobile"),
                    'cl_tp_accounting.old_engraved_id',
                    'cl_tp_accounting.user_id',
                    'cl_tp_accounting.shift_id',
                    'cl_tp_accounting.eq_id',
                ]);

            if ($tpAccounting->isEmpty()) {
                return response([
                    'status' => false,
                    'error' => 'No records found!'
                ], 404);
            }

            $dateWiseData = [];
            foreach ($tpAccounting as $transaction) {
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
