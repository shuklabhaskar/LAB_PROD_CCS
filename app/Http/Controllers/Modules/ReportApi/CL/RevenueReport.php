<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class RevenueReport extends Controller
{
    public function revenue(Request $request)
    {
        $from   = $request->input('from_date');
        $to     = $request->input('to_date');

        /* VALIDATION */
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date'   => 'required|date_format:Y-m-d H:i:s',
        ]);

        /* IF VALIDATION FAILS */
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()
            ]);
        } else {

            $clRevenue = [];

            /* TO GET NUMBER OF STATION AND STATION NAME */
            $stations = DB::table('station_inventory')
                ->select('stn_short_name', 'stn_id')
                ->orderBy('stn_id','ASC')
                ->get();

            foreach ($stations as $station){

                try {

                    /*FOR SV ISSUANCE RELOAD REFUND */
                    $svIssCount = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_sv_accounting.op_type_id','=',1)
                        ->count();

                    $svRefCount = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_sv_accounting.op_type_id','=',6)
                        ->count();

                    $svRelCount = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_sv_accounting.op_type_id','=',3)
                        ->count();

                    /* FOR TP ISSUANCE RELOAD AND REFUND */

                    $tpIssCount = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.op_type_id','=',1)
                        ->count();

                    $tpRefCount = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.op_type_id','=',6)
                        ->count();

                    $tpRelCount = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.op_type_id','=',3)
                        ->count();

                    $svIssAmount = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_sv_accounting.op_type_id','=',1)
                        ->sum('pos_chip_bal');

                    $svRefAmount = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_sv_accounting.op_type_id','=',6)
                        ->sum('pos_chip_bal');

                    $svRelAmount = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_sv_accounting.op_type_id','=',3)
                        ->sum('pos_chip_bal');

                    /* FOR TP ISSUANCE RELOAD AND REFUND */

                    $tpIssAmount = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.op_type_id','=',1)
                        ->sum('cl_tp_accounting.total_price');

                    $tpRefAmount = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.op_type_id','=',6)
                        ->sum('cl_tp_accounting.total_price');

                    $tpRelAmount = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.op_type_id','=',3)
                        ->sum('cl_tp_accounting.total_price');

                    $svGra = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->whereIn('cl_sv_accounting.op_type_id', [54, 61, 62, 63, 64, 65])
                        ->sum('pos_chip_bal');

                    $tpGra = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->whereIn('cl_tp_accounting.op_type_id', [54, 61, 62, 63, 64, 65])

                        ->sum('total_price');

                    $svRepAmount = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->whereIn('cl_sv_accounting.op_type_id', [11, 12, 13])
                        ->sum('cl_sv_accounting.pos_chip_bal');

                    $tpRepAmount = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->whereIn('cl_tp_accounting.op_type_id', [11, 12, 13])
                        ->sum('cl_tp_accounting.total_price');

                    $svIssueCancellation = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_sv_accounting.op_type_id','=',2)
                        ->sum('pos_chip_bal');

                    $tpIssueCancellation = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.op_type_id','=',2)
                        ->sum('cl_tp_accounting.total_price');

                    $svRelCancel = DB::table('cl_sv_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_sv_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_sv_accounting.txn_date)'), [$from, $to])
                        ->where('cl_sv_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_sv_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_sv_accounting.op_type_id','=',4)
                        ->sum('pos_chip_bal');

                    $tpRelCancel = DB::table('cl_tp_accounting')
                        ->join('pass_inventory','pass_inventory.pass_id','=','cl_tp_accounting.pass_id')
                        ->whereBetween(DB::raw('(cl_tp_accounting.txn_date)'), [$from, $to])
                        ->where('cl_tp_accounting.stn_id','=',$station->stn_id)
                        ->where('pass_inventory.status','=',true)
                        ->where('pass_inventory.is_test','=',false)
                        ->where('pass_inventory.media_type_id','=',2)
                        ->whereNot('cl_tp_accounting.pass_id','=',53)
                        ->whereNot('pass_inventory.pass_id','=',53)
                        ->where('cl_tp_accounting.op_type_id','=',4)
                        ->sum('cl_tp_accounting.total_price');

                    $clIssueAmount  = floatval($svIssAmount)            + floatval($tpIssAmount);
                    $clReloadAmount = floatval($svRelAmount)            + floatval($tpRelAmount);
                    $clRefundAmount = floatval($svRefAmount)            + floatval($tpRefAmount);
                    $clGRA          = floatval($svGra)                  + floatval($tpGra);
                    $clReplacement  = floatval($svRepAmount)            + floatval($tpRepAmount);
                    $clIssCancel    = floatval($svIssueCancellation)    + floatval($tpIssueCancellation);
                    $clRelCancel    = floatval($svRelCancel)            + floatval($tpRelCancel);

                    $data['stn_name']      = $station ->stn_short_name;
                    $data['stn_code']      = $station ->stn_id;
                    $data['sv_iss_count']  = floatval($svIssCount);
                    $data['sv_ref_count']  = floatval($svRefCount);
                    $data['sv_rel_count']  = floatval($svRelCount);
                    $data['tp_iss_count']  = floatval($tpIssCount);
                    $data['tp_ref_count']  = floatval($tpRefCount);
                    $data['tp_rel_count']  = floatval($tpRelCount);
                    $data['cl_revenue']    = $clIssueAmount + $clReloadAmount + $clGRA + $clReplacement + $clRefundAmount - $clIssCancel -$clRelCancel;

                    $clRevenue[] = $data;

                } catch (PDOException $e) {
                    $data['success'] = false;
                    $clRevenue[]     = $e;
                }
            }

            return response([
                'status' => true,
                'data'   => $clRevenue,
            ]);


        }
    }
}

