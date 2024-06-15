<?php

namespace App\Http\Controllers\Modules\ReportApi\MQR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PreviousDayReport extends Controller
{
    public function MqrPrevDay(Request $request)
    {
        $from = $request->input('from_date');
        $to = $request->input('to_date');

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

            $mqrData = [];

            /* TO GET NUMBER OF STATION AND STATION NAME */
            $stations = DB::table('station_inventory')
                ->select( 'stn_id')
                ->orderBy('stn_id', 'ASC')
                ->get();

            $entries = [];

            foreach ($stations as $station) {

                $data = [];

                /* FOR SJT */
                $sjtIssueCount = DB::table('msjt_ms_accounting')
                    ->whereBetween(DB::raw('(msjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 1)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->count();

                $sjtRefundCount = DB::table('msjt_ms_accounting')
                    ->whereBetween(DB::raw('(msjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 6)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->count();

                /* FOR SJT REVENUE */
                $sjtIssueAmount = DB::table('msjt_ms_accounting')
                    ->whereBetween(DB::raw('(msjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 1)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $sjtGra = DB::table('msjt_ms_accounting')
                    ->whereBetween(DB::raw('(msjt_ms_accounting.txn_date)'), [$from, $to])
                    ->whereIn('op_type_id', [54, 61, 62, 63, 64, 65])
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $sjtRevenue = $sjtIssueAmount + $sjtGra;

                /* FOR RJT */

                $rjtIssueCount = DB::table('mrjt_ms_accounting')
                    ->whereBetween(DB::raw('(mrjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 1)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('unit');

                $rjtRefundCount = DB::table('mrjt_ms_accounting')
                    ->whereBetween(DB::raw('(mrjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 6)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->count();

                /* FOR SJT REVENUE */
                $rjtIssueAmount = DB::table('mrjt_ms_accounting')
                    ->whereBetween(DB::raw('(mrjt_ms_accounting.txn_date)'), [$from, $to])
                    ->where('op_type_id', '=', 1)
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $rjtGra = DB::table('mrjt_ms_accounting')
                    ->whereBetween(DB::raw('(mrjt_ms_accounting.txn_date)'), [$from, $to])
                    ->whereIn('op_type_id', [54, 61, 62, 63, 64, 65])
                    ->where('src_stn_id', '=', $station->stn_id)
                    ->sum('total_price');

                $rjtRevenue = $rjtIssueAmount + $rjtGra;

                /* SEGREGATING STATION WISE DATA*/
                $data['station_id']= $station->stn_id;

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
                'data' => $entries,
            ]);

        }

    }

}
