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
                ->whereIn('cl_sv_accounting.pass_id', [23, 63, 73, 83,84])
                ->orderBy('cl_sv_accounting.txn_date', 'ASC')
                ->get([
                    'cl_sv_accounting.atek_id as order_id',
                    'cl_sv_accounting.txn_date',
                    'cl_sv_accounting.engraved_id',
                    'cl_sv_accounting.op_type_id',
                    'cl_sv_accounting.stn_id',
                    'cl_sv_accounting.pass_price',
                    'cl_sv_accounting.card_fee',
                    'cl_sv_accounting.card_sec as deposit',
                    'cl_sv_accounting.processing_fee as process_fee',
                    'cl_sv_accounting.total_price',
                    'cl_sv_accounting.pass_ref_chr as ref_chr',
                    'cl_sv_accounting.card_fee_ref_chr as csc_fee_ref_chr',
                    'cl_sv_accounting.card_sec_ref_chr as csc_dep_ref_chr',
                    'cl_sv_accounting.pre_chip_bal as pre_card_bal',
                    'cl_sv_accounting.pos_chip_bal as card_bal',
                    'cl_sv_accounting.media_type_id',
                    'cl_sv_accounting.product_id',
                    'cl_sv_accounting.pass_id',
                    'cl_sv_accounting.pass_expiry',
                    DB::raw("cl_status.pax_first_name as first_name"),
                    DB::raw("cl_status.pax_last_name as last_name"),
                    DB::raw("cl_status.pax_mobile as mobile_no"),
                    'cl_sv_accounting.pax_gen_type as gender',
                    'cl_sv_accounting.shift_id',
                    'cl_sv_accounting.user_id',
                    'cl_sv_accounting.eq_id',
                    'cl_sv_accounting.pay_type_id',
                    'cl_sv_accounting.pay_ref',
                    'cl_sv_accounting.is_test',
                    'cl_sv_accounting.old_engraved_id',
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
                ->whereIn('cl_tp_accounting.pass_id', [23, 63, 73, 83, 24, 64])
                ->orderBy('cl_tp_accounting.txn_date', 'ASC')
                ->get([
                    'cl_tp_accounting.atek_id as order_id',
                    'cl_tp_accounting.txn_date',
                    'cl_tp_accounting.engraved_id',
                    'cl_tp_accounting.op_type_id',
                    'cl_tp_accounting.stn_id',
                    'cl_tp_accounting.pass_price',
                    'cl_tp_accounting.card_fee',
                    'cl_tp_accounting.card_sec as deposit',
                    'cl_tp_accounting.processing_fee as process_fee',
                    'cl_tp_accounting.total_price',
                    'cl_tp_accounting.pass_ref_chr as refund_chr',
                    'cl_tp_accounting.card_fee_ref_chr as csc_fee_ref_chr',
                    'cl_tp_accounting.card_sec_ref_chr as csc_dep_ref_chr',
                    'cl_tp_accounting.num_trips',
                    'cl_tp_accounting.rem_trips as card_bal',
                    'cl_tp_accounting.media_type_id',
                    'cl_tp_accounting.product_id',
                    'cl_tp_accounting.pass_id',
                    'cl_tp_accounting.pass_expiry',
                    'cl_tp_accounting.src_stn_id as source_stn',
                    'cl_tp_accounting.des_stn_id as destination_stn',
                    DB::raw("cl_status.pax_first_name as first_name"),
                    DB::raw("cl_status.pax_last_name as last_name"),
                    DB::raw("cl_status.pax_mobile as mobile_no"),
                    'cl_tp_accounting.pax_gen_type as gender',
                    'cl_tp_accounting.shift_id',
                    'cl_tp_accounting.user_id',
                    'cl_tp_accounting.eq_id',
                    'cl_tp_accounting.pay_type_id',
                    'cl_tp_accounting.pay_ref',
                    'cl_tp_accounting.is_test',
                    'cl_tp_accounting.old_engraved_id',
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
