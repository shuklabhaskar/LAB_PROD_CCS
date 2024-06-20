<?php

namespace App\Http\Controllers\Modules\ReportApi\CL\AfcAuditApi;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TripPassAuditApi extends Controller
{

    function index($startDate, $endDate)
    {

        set_time_limit(0);

        $result = [];

        $valTrans = DB::table('cl_tp_validation')
            ->whereIn('trip_balance', [45, 500])
            ->whereIn('pass_id',[23,63])
            ->whereBetween('txn_date', [$startDate, $endDate])
            ->orderBy('txn_date', 'DESC')
            ->select(['engraved_id', 'trip_balance', 'txn_date'])
            ->get();

        foreach ($valTrans as $valTran) {

            $valTxnDate = Carbon::parse($valTran->txn_date);

            $accTran =  DB::table('cl_tp_accounting')
                ->where('engraved_id', '=', $valTran->engraved_id)
                ->whereIn('op_type_id', [1, 3])
                ->whereIn('pass_id',[23,63])
                ->whereBetween('txn_date', [
                    $valTxnDate->copy()->subDays(5),
                    $valTxnDate->copy()->addDays(5)
                ])
                ->first();

            if ($accTran == null) {
                $result[$valTran->engraved_id] = $valTran;
            }

        }

        return response()->json([
            'status' => true,
            'data' => $result ?: "No Data Found",
        ]);

    }

}
