<?php /** @noinspection UnknownColumnInspection */

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TripPassExitRevenue extends Controller
{
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
                ->whereIn('pass_id',  [23,24])
                ->where('val_type_id', '=', 2)
                ->orderBy('txn_date', 'DESC')
                ->pluck('engraved_id');

            $sum = 0;

            foreach ($engravedIds as $engravedId) {

                $accTrans = DB::table('cl_tp_accounting')
                    ->where('pass_id', '=', 23)
                    ->where('op_type_id', [1, 3, 11, 12, 13])
                    ->where('engraved_id', $engravedId)
                    ->orderBy('txn_date', 'DESC')
                    ->first();

                if ($accTrans != null) {

                    if ($accTrans->op_type_id == 1 || $accTrans->op_type_id == 3) {
                        $sum += ($accTrans->pass_price / $accTrans->num_trips);
                        continue;
                    }

                    if ($accTrans->op_type_id == 11 || $accTrans->op_type_id == 12 || $accTrans->op_type_id == 13) {

                        $accRepTrans = $accTrans;

                        while (true) {

                            $repTrans = DB::table('cl_tp_accounting')
                                ->where('engraved_id', '=', $accRepTrans->old_engraved_id)
                                ->whereIn('op_type_id', [1, 3, 11, 12, 13])
                                ->select(['old_engraved_id', 'engraved_id', 'pass_price', 'num_trips', 'op_type_id'])
                                ->first();

                            if (
                                $repTrans != null &&
                                ($repTrans->op_type_id == 11 || $repTrans->op_type_id == 12 || $repTrans->op_type_id == 13)
                            ) {
                                $accRepTrans = $repTrans;
                                continue;
                            }

                            if ($repTrans != null) {
                                $sum += ($repTrans->pass_price / $repTrans->num_trips);
                            }

                            break;

                        }

                    }

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

                    $pass->trip_count == 0 ?: $sum +=  ($fare / $pass->trip_count);

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
                ->whereIn('pass_id',  [23,24])
                ->where('pass_expiry', '=', $processDate . " 01:10:00")
                ->whereIn('op_type_id', [1, 3])
                ->orderBy('txn_date', 'DESC')
                ->get();

            $sum = 0;

            foreach ($accTrans as $trans) {

                $repTrans = DB::table('cl_tp_accounting')
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

                $unitPrice = ($trans->pass_price / $trans->num_trips);

                $validationWithZeroTrips = DB::table('cl_tp_validation')
                    ->where('txn_date', '<=', $processDate . " 01:10:00")
                    ->where('engraved_id', $engravedId)
                    ->where('trip_balance', 0)
                    ->orderBy('txn_date', 'DESC')
                    ->first();

                if ($validationWithZeroTrips != null) {
                    $staleAmount = $unitPrice * $validationWithZeroTrips->trip_balance;
                } else {
                    $validation = DB::table('cl_tp_validation')
                        ->where('txn_date', '<=', $processDate . " 01:10:00")
                        ->where('engraved_id', $engravedId)
                        ->orderBy('txn_date', 'DESC')
                        ->first();
                    if ($validation != null) {
                        $staleAmount = $unitPrice * $validation->trip_balance;
                    } else {
                        $staleAmount = $unitPrice * $trans->num_trips;
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

    /* TP STALE REVENUE FROM CL INDRA REP TABLE PASS ID 23 */
    function tpStaleRevenueIndra(Request $request)
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

            $accTrans = DB::table('cl_indra_rep')
                ->where('pass_id', '=', 23)
                ->where('pass_expiry', '=', $processDate . " 01:10:00")
                ->where('tp_balance', '!=', 0)
                ->orderBy('txn_date', 'DESC')
                ->get();

            $sum = 0;

            foreach ($accTrans as $trans) {

                $passExpiry = Carbon::parse($trans->pass_expiry);
                $createdAt = Carbon::parse($trans->created_at);

                if ($passExpiry < $createdAt) continue;

                $repTrans = DB::table('cl_tp_accounting')
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


                $sourceId = $trans->src_stn_id;
                $destinationId = $trans->des_stn_id;

                $pass = DB::table('pass_inventory')
                    ->where('pass_id', '=', $trans->pass_id)
                    ->select('fare_table_id', 'trip_count')
                    ->first();

                $fare = DB::table('fare_table')
                    ->where('fare_table_id', '=', $pass->fare_table_id)
                    ->where('source_id', '=', $sourceId)
                    ->where('destination_id', '=', $destinationId)
                    ->value('fare');

                $unitPrice = ($fare / $pass->trip_count);

                $validationWithZeroTrips = DB::table('cl_tp_validation')
                    ->where('txn_date', '<=', $processDate . " 01:10:00")
                    ->where('engraved_id', $engravedId)
                    ->where('trip_balance', 0)
                    ->orderBy('txn_date', 'DESC')
                    ->first();


                if ($validationWithZeroTrips != null) {
                    $staleAmount = $unitPrice * $validationWithZeroTrips->trip_balance;
                } else {

                    $validation = DB::table('cl_tp_validation')
                        ->where('txn_date', '<=', $processDate . " 01:10:00")
                        ->where('engraved_id', $engravedId)
                        ->orderBy('txn_date', 'DESC')
                        ->first();

                    if ($validation != null) {
                        $staleAmount = $unitPrice * $validation->trip_balance;
                    } else {
                        $staleAmount = $unitPrice * $trans->tp_balance;
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

    /*TP STALE FOR UNLIMITED TRIP PASS ID 63 */
    function tpStaleUL(Request $request)
    {

        // SET TIMEOUT
        set_time_limit(0);

        $startDate = Carbon::parse('01-11-2023');
        $endDate = Carbon::now();

        // FILL DATA
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            $processDate = $date->format('Y-m-d');

            $result = DB::selectOne("
                    SELECT (
                        SUM(CASE WHEN pass_id = 63 AND op_type_id IN (1, 3) THEN ROUND(CAST(FLOAT8 ((pass_price) / 30) AS NUMERIC), 2) ELSE 0 END) -
                        SUM(CASE WHEN pass_id = 63 AND op_type_id IN (2, 4, 6) THEN ROUND(CAST(FLOAT8 ((pass_price) / 30) AS NUMERIC), 2) ELSE 0 END)
                    ) AS TOTAL_AMOUNT
                    FROM cl_tp_accounting
                    WHERE DATE(txn_date) = ?
                ", [$processDate]);

            if ($result != null) {

                $this->insertOrUpdate(
                    $processDate,
                    $result->total_amount ?: 0,
                    0
                );

            }

        }

        $updateTrans = DB::table('cl_tp_ul_stale_exit')
            ->orderBy('date', 'ASC')
            ->get();

        // UPDATE STABLE AMOUNT
        foreach ($updateTrans as $tran) {

            $startCalDate = Carbon::parse($tran->date);
            $endCalDate = Carbon::parse($tran->date)->subDays(30);

            $staleAmount = DB::table('cl_tp_ul_stale_exit')
                ->whereBetween('date', [$endCalDate, $startCalDate])
                ->sum('distribution_amount');

            $this->insertOrUpdate(
                $tran->date,
                $tran->distribution_amount ?: 0,
                $staleAmount ?: 0
            );

        }

        // GET RESPONSE
        $startDate = Carbon::parse($request->input('from_date'));
        $endDate = Carbon::parse($request->input('to_date'));

        $responseData = DB::table('cl_tp_ul_stale_exit')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date','DESC')
            ->get(['date', 'stale_amount']);

        return response([
            'status' => true,
            'data' => $responseData
        ]);

    }

    function insertOrUpdate($date, $disAmount, $stableAmount)
    {
        $isAlreadyExit = DB::table('cl_tp_ul_stale_exit')
            ->where('date', '=', $date)
            ->first();

        if ($isAlreadyExit != null) {
            DB::table('cl_tp_ul_stale_exit')
                ->where('date', $date)
                ->update([
                    'distribution_amount' => round($disAmount, 2),
                    'stale_amount' => round($stableAmount, 2)
                ]);
        } else {
            DB::table('cl_tp_ul_stale_exit')->insert([
                'date' => $date,
                'distribution_amount' => round($disAmount, 2),
                'stale_amount' => round($stableAmount, 2)
            ]);
        }

    }

}




