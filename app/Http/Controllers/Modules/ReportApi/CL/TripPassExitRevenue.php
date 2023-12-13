<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TripPassExitRevenue extends Controller
{
    /* TRIP PASS EXIT REVENUE FOR PASS ID 23 */
    function tpExitRevenue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        set_time_limit(0);

        $startDate = Carbon::parse($request->input('from_date'));
        $endDate = Carbon::parse($request->input('to_date'));

        $response = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            $processDate = $date->format('Y-m-d');

            $engravedIds = DB::table('cl_tp_validation')
                ->whereRaw("TO_CHAR(txn_date, 'YYYY-MM-DD') = ?", [$processDate])
                ->where('pass_id', '=', 23)
                ->where('val_type_id', '=', 2)
                ->orderBy('txn_date', 'ASC')
                ->pluck('engraved_id');

            $sum = 0;

            foreach ($engravedIds as $engravedId) {

                $accTrans = DB::table('cl_tp_accounting')
                    ->where('pass_id', '=', 23)
                    ->where('op_type_id', [1, 3])
                    ->where('engraved_id', $engravedId)
                    ->first();

                if ($accTrans != null) {
                    $sum += ($accTrans->pass_price / $accTrans->num_trips);
                    continue;
                }

                $repTrans = DB::table('cl_indra_rep')
                    ->where('engraved_id', '=', $engravedId)
                    ->orderBy('txn_date', 'ASC')
                    ->first();

                if ($repTrans != null) {

                    $sourceId = $repTrans->src_stn_id;
                    $destinationId = $repTrans->des_stn_id;

                    $pass = DB::table('pass_inventory')
                        ->where('pass_id', '=', $repTrans->pass_id)
                        ->select('fare_table_id', 'trip_count')
                        ->first();

                    $fare = DB::table('fare_table')
                        ->where('fare_table_id', '=', $pass->fare_table_id)
                        ->where('source_id', '=', $sourceId)
                        ->where('destination_id', '=', $destinationId)
                        ->value('fare');

                    $sum += ($fare / $pass->trip_count);

                }

            }

            $response[] = [
                'date' => $processDate,
                'amount' => number_format($sum, 2)
            ];

        }


        return response([
            'status' => true,
            'data' => $response
        ]);
    }

    /* TP STALE REVENUE FOR ATEK SYSTEM PASS ID 23*/
    function tpStaleRevenueAtek(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        set_time_limit(0);

        $startDate = Carbon::parse($request->input('from_date'));
        $endDate = Carbon::parse($request->input('to_date'));

        $response = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            $processDate = $date->format('Y-m-d');

            $accTrans = DB::table('cl_tp_accounting')
                ->where('pass_id', '=', 23)
                ->where('pass_expiry', '=', $processDate . " 01:10:00")
                ->whereIn('op_type_id', [1, 3])
                ->orderBy('txn_date', 'DESC')
                ->get();

            $sum = 0;

            foreach ($accTrans as $trans) {

                $validation = DB::table('cl_tp_validation')
                    ->where('txn_date', '<=', $processDate . " 01:10:00")
                    ->where('engraved_id', $trans->engraved_id)
                    ->orderBy('txn_date', 'DESC')
                    ->first();

                $unitPrice = ($trans->pass_price / $trans->num_trips);

                if ($validation != null) {
                    $staleAmount = $unitPrice * $validation->trip_balance;
                } else {
                    $staleAmount = $unitPrice * $trans->num_trips;
                }

                $sum += $staleAmount;

            }

            $response[] = [
                'date' => $processDate,
                'amount' => number_format($sum, 2)
            ];

        }

        return response([
            'status' => true,
            'data' => $response
        ]);

    }

    /* TP STALE REVENUE FROM CL INDRA REP TABLE PASS ID 23 */
    function tpStaleRevenueIndra(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d',
            'to_date'   => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        set_time_limit(0);

        $startDate  = Carbon::parse($request->input('from_date'));
        $endDate    = Carbon::parse($request->input('to_date'));

        $response = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            $processDate = $date->format('Y-m-d');

            $accTrans = DB::table('cl_indra_rep')
                ->where('pass_id', '=', 23)
                ->where('pass_expiry', '=', $processDate . " 01:10:00")
                ->where('tp_balance','!=',0)
                ->orderBy('txn_date', 'DESC')
                ->get();

            $sum = 0;

            foreach ($accTrans as $trans) {

                $passExpiry = Carbon::parse($trans->pass_expiry);
                $createdAt  = Carbon::parse($trans->created_at);

                if ($passExpiry < $createdAt) continue;

                $sourceId       = $trans->src_stn_id;
                $destinationId  = $trans->des_stn_id;

                $pass = DB::table('pass_inventory')
                    ->where('pass_id', '=', $trans->pass_id)
                    ->select('fare_table_id', 'trip_count')
                    ->first();

                $fare = DB::table('fare_table')
                    ->where('fare_table_id', '=', $pass->fare_table_id)
                    ->where('source_id', '=', $sourceId)
                    ->where('destination_id', '=', $destinationId)
                    ->value('fare');

                $validation = DB::table('cl_tp_validation')
                    ->where('txn_date', '<=', $processDate . " 01:10:00")
                    ->where('engraved_id', $trans->engraved_id)
                    ->orderBy('txn_date', 'DESC')
                    ->first();

                $unitPrice = ($fare / $pass->trip_count);

                if ($validation != null) {
                    $staleAmount = $unitPrice * $validation->trip_balance;
                } else {
                    $staleAmount = $unitPrice * $trans->tp_balance;
                }

                $sum += $staleAmount;

            }

            $response[] = [
                'date' => $processDate,
                'amount' => number_format($sum, 2)
            ];

        }

        return response([
            'status' => true,
            'data' => $response
        ]);

    }

    /*TP STALE FOR UNLIMITED TRIP PASS ID 73 83 */
    function tpStaleUL(Request $request)
    {

        set_time_limit(0);

        $startDate = Carbon::parse($request->input('from_date'));
        $endDate = Carbon::parse($request->input('to_date'));

        $previousDateSum = 0;
        $numberOfDays = $startDate->diffInDays($endDate);
        $response = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            $sum = 0;

            $processDate = $date->format('Y-m-d');

            $accTrans = DB::table('cl_tp_accounting')
                ->whereIn('op_type_id', [1, 3])
                ->whereRaw("TO_CHAR(txn_date, 'YYYY-MM-DD') = ?", [$processDate])
                ->where('pass_id', '=', 63)
                ->orderBy('txn_date', 'DESC')
                ->get();

            foreach ($accTrans as $trans) {

                $pricePerDay = $trans->pass_price / $numberOfDays;
                $sum += $pricePerDay;
            }

            $response[] = [
                'date' => $processDate,
                'amount' => number_format(($previousDateSum + $sum), 2)
            ];

            $previousDateSum += $sum;

        }

        return response([
            'status' => true,
            'data' => $response
        ]);

    }

}


