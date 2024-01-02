<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoreValueExitRevenue extends Controller
{
    function storeValueStaleIndra(Request $request)
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
            $sum = 0;

            $repTrans = DB::table('cl_indra_rep')
                ->whereRaw('pass_expiry + INTERVAL \'180 days\' = ?', [$processDate . " 01:10:00"])
                ->whereIn('pass_id', [83, 73])
                ->orderBy('txn_date', "DESC")
                ->get();

            foreach ($repTrans as $trans) {

                $passExpiry = Carbon::parse($trans->pass_expiry);
                $graceExpiry = Carbon::parse($trans->pass_expiry)->addDays(180);

                $accTrans = DB::table('cl_sv_accounting')
                    ->whereBetween('txn_date', [$passExpiry, $graceExpiry])
                    ->whereIn('op_type_id',[3,6])
                    ->where('engraved_id', '=', $trans->engraved_id)
                    ->first();

                if ($accTrans == null) {

                    $validation = DB::table('cl_indra_rep')
                        ->where('engraved_id', '<=', $trans->engraved_id)
                        ->select('sv_balance')
                        ->first();

                    if ($validation != null) {
                        $sum += $trans->sv_balance;
                    }

                } /*else {
                    $sum += $trans->sv_balance;
                }*/

            }

            $response[] = [
                'date' => $processDate,
                'amount' => round($sum, 2)
            ];

        }

        return response([
            'status' => true,
            'data' => $response
        ]);
    }

    function storeValueStaleAtek(Request $request)
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

            $accTrans = DB::table('cl_sv_accounting')
                ->whereIn('pass_id', [83, 73])
                ->whereRaw('pass_expiry + INTERVAL \'180 days\' = ?', [$processDate . " 01:10:00"])
                ->whereIn('op_type_id', [1, 3])
                ->orderBy('txn_date', 'DESC')
                ->get();

            $sum = 0;

            foreach ($accTrans as $trans) {

                $repTrans = DB::table('cl_sv_accounting')
                    ->where('old_engraved_id', '=', $trans->engraved_id)
                    ->whereBetween('pass_expiry', [$trans->txn_date, $processDate . " 01:10:00"])
                    ->orderBy('txn_date', 'DESC')
                    ->first();

                $engravedId = null;

                if ($repTrans != null) {
                    $engravedId = $repTrans->engraved_id;
                } else {
                    $engravedId = $trans->engraved_id;
                }

                $unitPrice = ($trans->pass_price / $trans->pos_chip_balance);

                $validationWithZeroTrips = DB::table('cl_sv_validation')
                    ->where('txn_date', '<=', $processDate . " 01:10:00")
                    ->where('engraved_id', $engravedId)
                    ->where('trip_balance', 0)
                    ->orderBy('txn_date', 'DESC')
                    ->first();

                if ($validationWithZeroTrips != null) {
                    $staleAmount = $unitPrice * $validationWithZeroTrips->trip_balance;
                } else {
                    $validation = DB::table('cl_sv_validation')
                        ->where('txn_date', '<=', $processDate . " 01:10:00")
                        ->where('engraved_id', $engravedId)
                        ->orderBy('txn_date', 'DESC')
                        ->first();
                    if ($validation != null) {
                        $staleAmount = $unitPrice * $validation->chip_balance;
                    } else {
                        $staleAmount = $unitPrice * $trans->pos_chip_balance;
                    }
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

    function svExitRevenue(Request $request)
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


        $query = "SELECT a.DT, a.SV_AMT as PURSE_EXIT
    FROM (
        SELECT
            CASE
                WHEN TO_CHAR(a.txn_date, 'hh24miss') >= '0' AND TO_CHAR(a.txn_date, 'hh24miss') <= '010959'
                THEN TO_CHAR((a.txn_date) - interval '1 DAY', 'yyyymmdd')
                ELSE TO_CHAR(a.txn_date, 'yyyymmdd')
            END AS DT,
            SUM(
                CASE
                    WHEN val_type_id = 2 AND pass_id IN (73, 83)
                    THEN amt_deducted
                    ELSE 0
                END
            ) AS SV_AMT
        FROM cl_sv_validation a
        RIGHT JOIN station_inventory b ON a.stn_id = b.stn_id
        WHERE txn_date >= ? AND txn_date <= ?
        GROUP BY
            CASE
                WHEN TO_CHAR(a.txn_date, 'hh24miss') >= '0' AND TO_CHAR(a.txn_date, 'hh24miss') <= '010959'
                THEN TO_CHAR((a.txn_date) - interval '1 DAY', 'yyyymmdd')
                ELSE TO_CHAR(a.txn_date, 'yyyymmdd')
            END
    ) AS a
    ORDER BY a.DT
";


        $results = DB::select($query, [$startDate, $endDate]);


        return response()->json($results);


    }

}

