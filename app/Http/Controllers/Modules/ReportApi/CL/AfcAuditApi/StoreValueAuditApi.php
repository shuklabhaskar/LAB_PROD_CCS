<?php

namespace App\Http\Controllers\Modules\ReportApi\CL\AfcAuditApi;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class StoreValueAuditApi extends Controller
{

    function index($startDate, $endDate)
    {
        set_time_limit(0);

        $result = [];

        $valTrans = DB::table('cl_sv_validation')
            ->whereIn('pass_id',[73, 83])
            ->whereBetween('txn_date', [$startDate, $endDate])
            ->orderBy('engraved_id', 'DESC')
            ->distinct('engraved_id')
            ->select(['engraved_id', 'chip_balance', 'txn_date'])
            ->get();

        foreach ($valTrans as $valTran) {

            $valTxnDate = Carbon::parse($valTran->txn_date);

            $eidValTrans =  DB::table('cl_sv_validation')
                ->where('engraved_id',$valTran->engraved_id)
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

                    $accTran =  DB::table('cl_sv_accounting')
                        ->where('engraved_id', '=', $valTran->engraved_id)
                        ->whereIn('op_type_id', [1, 3])
                        ->whereIn('pass_id',[73, 83])
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
            'data' => $result ?: "No Data Found",
        ]);

    }

}
