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

                $accTrans = DB::table('cl_indra_rep')
                    ->whereBetween('txn_date', [$passExpiry, $graceExpiry])
                    ->where('engraved_id', '=', $trans->engraved_id)
                    ->first();

                if ($accTrans == null) {

                    $validation = DB::table('cl_sv_validation')
                        ->where('txn_date', '<=', $graceExpiry)
                        ->orderBy('txn_date', "DESC")
                        ->first();

                    if ($validation != null) {
                        $sum += $validation->chip_balance;
                    }

                } else {
                    $sum += $trans->sv_balance;
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
}
