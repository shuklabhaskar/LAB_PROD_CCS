<?php

namespace App\Http\Controllers\Modules\ReportApi\PQR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class PQRPreviousDayReport extends Controller
{
    public function PqrPrevDay(Request $request)
    {
        $from = $request->input('from_date');
        $to   = $request->input('to_date');

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

            set_time_limit(0);

            /* TO GET NUMBER OF STATION AND STATION NAME */
            $stations = DB::table('station_inventory')
                ->select( 'stn_id')
                ->orderBy('stn_id', 'ASC')
                ->get();

            $entries = [];

            foreach ($stations as $station) {

                $data = [];

                /* FOR SJT */
                $sjtIssueCount = DB::table('psjt_ms_accounting')
                    ->whereBetween(DB::raw('(psjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 1)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('units');

                $sjtRefundCount = DB::table('psjt_ms_accounting')
                    ->whereBetween(DB::raw('(psjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 6)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->count();

                /* FOR SJT REVENUE */
                $sjtIssueAmount = DB::table('psjt_ms_accounting')
                    ->whereBetween(DB::raw('(psjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 1)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $sjtGra = DB::table('psjt_ms_accounting')
                    ->whereBetween(DB::raw('(psjt_ms_accounting.txn_date)'), [$from, $to])
                    ->whereNotIn('op_type_id', [1, 6])
                    ->where('des_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $sjtRefundAmount = DB::table('psjt_ms_accounting')
                    ->whereBetween(DB::raw('(psjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 6)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $sjtRevenue = $sjtIssueAmount + $sjtGra - $sjtRefundAmount;

                /* FOR RJT */
                $rjtIssueCount = DB::table('prjt_ms_accounting')
                    ->whereBetween(DB::raw('(prjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 1)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('units');

                $rjtRefundCount = DB::table('prjt_ms_accounting')
                    ->whereBetween(DB::raw('(prjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 6)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->count();

                /* FOR SJT REVENUE */
                $rjtIssueAmount = DB::table('prjt_ms_accounting')
                    ->whereBetween(DB::raw('(prjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 1)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $rjtGra = DB::table('prjt_ms_accounting')
                    ->whereBetween(DB::raw('(prjt_ms_accounting.txn_date)'), [$from, $to])
                    ->whereNotIn('op_type_id', [1, 6])
                    ->where('des_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $rjtRefundAmount = DB::table('prjt_ms_accounting')
                    ->whereBetween(DB::raw('(prjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 6)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $rjtRevenue = $rjtIssueAmount + $rjtGra - $rjtRefundAmount;

                /* SEGREGATING STATION WISE DATA*/
                $data['station_id'] = $station->stn_id;

                /*FOR SJT ONLY */
                $data['sjt']['issue_count']   = $sjtIssueCount;
                $data['sjt']['refund_count']  = $sjtRefundCount;
                $data['sjt']['total_revenue'] = $sjtRevenue;
                $data['sjt']['ridership']     = $sjtIssueCount;

                /* FOR RJT ONLY */
                $data['rjt']['rjt_issue_count']  = $rjtIssueCount;
                $data['rjt']['rjt_refund_count'] = $rjtRefundCount;
                $data['rjt']['total_revenue']    = $rjtRevenue;
                $data['rjt']['ridership']        = $rjtIssueCount*2;

                $entries[] = $data;

            }

            return response([
                'status' => true,
                'data'   => $entries,
            ]);

        }

    }

}
