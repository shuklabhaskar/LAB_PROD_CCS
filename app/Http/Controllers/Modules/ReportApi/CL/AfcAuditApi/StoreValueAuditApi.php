<?php

namespace App\Http\Controllers\Modules\ReportApi\CL\AfcAuditApi;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class StoreValueAuditApi extends Controller
{

    function index($startDate, $endDate)
    {
        set_time_limit(0);

        $result = [];

        $valTrans = DB::table('cl_sv_validation')
            ->whereIn('pass_id', [73, 83])
            ->whereBetween('txn_date', [$startDate, $endDate])
            ->orderBy('engraved_id', 'DESC')
            ->distinct('engraved_id')
            ->select(['engraved_id', 'chip_balance', 'txn_date'])
            ->get();

        foreach ($valTrans as $valTran) {

            $valTxnDate = Carbon::parse($valTran->txn_date);

            $eidValTrans = DB::table('cl_sv_validation')
                ->where('engraved_id', $valTran->engraved_id)
                ->whereBetween('txn_date', [$startDate, $endDate])
                ->orderBy('txn_date', 'ASC')
                ->get();

            $lastTxnAmount = 0;

            foreach ($eidValTrans as $eidValTran) {

                if ($lastTxnAmount == 0) {
                    $lastTxnAmount = $eidValTran->chip_balance;
                    continue;
                }

                if ($lastTxnAmount < $eidValTran->chip_balance) {

                    $accTran = DB::table('cl_sv_accounting')
                        ->where('engraved_id', '=', $valTran->engraved_id)
                        ->whereIn('op_type_id', [1, 3])
                        ->whereIn('pass_id', [73, 83])
                        ->whereBetween('txn_date', [
                            $valTxnDate->copy()->subDays(5),
                            $valTxnDate->copy()->addDays(5)
                        ])
                        ->first();

                    if ($accTran == null) {
                        $result[$valTran->engraved_id] = $valTran;
                    }

                }

            }

        }

        return response()->json([
            'status' => true,
            'data'   => $result ?: "No Data Found",
        ]);

    }

    function lag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date'   => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        $data = [];
        $count = 0;

        set_time_limit(0);

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = "
            WITH TransactionWithRowNumber AS (
                SELECT
                    engraved_id,
                    txn_date,
                    chip_balance,
                    LAG(chip_balance) OVER (PARTITION BY engraved_id ORDER BY txn_date) AS last_balance,
                    ROW_NUMBER() OVER (PARTITION BY engraved_id ORDER BY txn_date DESC) AS rn
                FROM
                    cl_sv_validation
                WHERE
                    val_type_id = 1
            )
            SELECT
                engraved_id,
                txn_date,
                chip_balance AS current_balance,
                last_balance AS last_entry_balance
            FROM
                TransactionWithRowNumber
            WHERE
                rn = 1
                AND txn_date >= ?
                AND txn_date <= ?
            ORDER BY
                engraved_id, txn_date DESC;
        ";

        $transactions = DB::select($query, [$fromDate, $toDate]);


        foreach ($transactions as $transaction) {

            $negativeBalance = $transaction->last_entry_balance - $transaction->current_balance;

            if ($transaction->last_entry_balance === null){
                continue;
            }

            if ($negativeBalance < 0) {

                $checkEngraved = DB::table('cl_sv_accounting')
                    ->where('op_type_id', '=', 3)
                    ->where('engraved_id', '=', $transaction->engraved_id)
                    ->whereBetween('txn_date', [
                        date('Y-m-d', strtotime($transaction->txn_date . ' -1 days')),
                        date('Y-m-d', strtotime($transaction->txn_date))
                    ])
                    ->exists();


                if ($checkEngraved === false) {

                    $data[] = [
                            'engraved_id'        => $transaction->engraved_id,
                            'txn_date'           => $transaction->txn_date,
                            'last_entry_balance' => $transaction->last_entry_balance,
                            'current_balance'    => $transaction->current_balance,
                        ];

                    $count++;

                }

            }

        }

        return response()->json([
            'status' => true,
            'count'  => $count,
            'data'   => $data,
        ]);

    }

}
