<?php

namespace App\Http\Controllers\Modules\ReportApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class Revenue extends Controller
{
    public function index(Request $request)
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

            $cardData = [];

            /* TO GET NUMBER OF STATION AND STATION NAME */
            $stations = DB::table('station_inventory')->select('stn_name', 'stn_id')->orderBy('stn_id','ASC')->get();

            $totalRevenue = DB::table('ol_sv_validation')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('val_type_id', '=', 2)
                ->where('pass_id','=',82)
                ->sum('amt_deducted');

            $penAmount = DB::table('ol_sv_accounting as osa')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->whereIn('op_type_id', [61, 62, 63, 64, 65])
                ->sum('total_price');

            /* PPBL TOTAL REVENUE */
            $ppblTotalRevenue = DB::table('ol_sv_validation')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('val_type_id', '=', 2)
                ->where('card_mask_no','LIKE','8173'.'%')
                ->where('pass_id','=',82)
                ->sum('amt_deducted');

            $ppblPenAmount = DB::table('ol_sv_accounting as osa')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->where('card_mask_no','LIKE','8173'.'%')
                ->whereIn('op_type_id', [61, 62, 63, 64, 65])
                ->sum('total_price');

            /* SBI TOTAL REVENUE */
            $sbiTotalRevenue = DB::table('ol_sv_validation')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('val_type_id', '=', 2)
                ->where('card_mask_no','LIKE','8174'.'%')
                ->where('pass_id','=',82)
                ->sum('amt_deducted');

            $sbiPenAmount = DB::table('ol_sv_accounting as osa')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->where('card_mask_no','LIKE','8174'.'%')
                ->whereIn('op_type_id', [61, 62, 63, 64, 65])
                ->sum('total_price');

            /* SBI TOTAL REVENUE */
            $otherTotalRevenue = DB::table('ol_sv_validation')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('val_type_id', '=', 2)
                ->where('card_mask_no','NOT LIKE','8173'.'%')
                ->where('card_mask_no','NOT LIKE','8174'.'%')
                ->where('pass_id','=',82)
                ->sum('amt_deducted');

            $otherPenAmount = DB::table('ol_sv_accounting as osa')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->where('card_mask_no','NOT LIKE','8173'.'%')
                ->where('card_mask_no','NOT LIKE','8174'.'%')
                ->whereIn('op_type_id', [61, 62, 63, 64, 65])
                ->sum('total_price');

            $ttlCardFee = DB::table('ol_card_sale as ocs')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->sum('total_price');


            $ttlTotalPrice = DB::table('ol_sv_accounting as osa')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->whereIn('pay_type_id', [1, 2, 3])
                ->sum('total_price');


            $totalCollection = $ttlCardFee + $ttlTotalPrice;

            foreach ($stations as $stn_data) {

                try {

                    /* TOTAL CARD FEE */
                    $cardFee = DB::table('ol_card_sale as ocs')
                        ->where('ocs.stn_id', '=', $stn_data->stn_id)
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->sum('total_price');

                    /* ADDITION OF ALL TOTAL PRICE */
                    $totalPrice = DB::table('ol_sv_accounting as osa')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->where('pass_id','=',82)
                        ->where('osa.stn_id', '=', $stn_data->stn_id)
                        ->whereIn('pay_type_id', [1, 2, 3])
                        ->sum('total_price');

                    /* PPBL  REVENUE */
                    $ppbl_revenue = DB::table('ol_sv_validation')
                        ->where('stn_id', '=', $stn_data->stn_id)
                        ->where('pass_id','=',82)
                        ->where('val_type_id', '=', 2)
                        ->where('card_mask_no','LIKE','8173'.'%')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->sum('amt_deducted');

                    $ppbl_stnPenAmount = DB::table('ol_sv_accounting as osa')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->where('pass_id','=',82)
                        ->where('card_mask_no','LIKE','8173'.'%')
                        ->whereIn('op_type_id', [61, 62, 63, 64, 65])
                        ->sum('total_price');

                    /* SBI  REVENUE */
                    $sbi_revenue = DB::table('ol_sv_validation')
                        ->where('stn_id', '=', $stn_data->stn_id)
                        ->where('pass_id','=',82)
                        ->where('val_type_id', '=', 2)
                        ->where('card_mask_no','LIKE','8174'.'%')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->sum('amt_deducted');

                    $sbi_stnPenAmount = DB::table('ol_sv_accounting as osa')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->where('pass_id','=',82)
                        ->whereIn('op_type_id', [61, 62, 63, 64, 65])
                        ->where('card_mask_no','LIKE','8174'.'%')
                        ->sum('total_price');

                    /* OTHER  REVENUE */
                    $other_revenue = DB::table('ol_sv_validation')
                        ->where('stn_id', '=', $stn_data->stn_id)
                        ->where('pass_id','=',82)
                        ->where('val_type_id', '=', 2)
                        ->where('card_mask_no','NOT LIKE','8173'.'%')
                        ->where('card_mask_no','NOT LIKE','8174'.'%')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->sum('amt_deducted');

                    $other_stnPenAmount = DB::table('ol_sv_accounting as osa')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->where('pass_id','=',82)
                        ->whereIn('op_type_id', [61, 62, 63, 64, 65])
                        ->where('card_mask_no','NOT LIKE','8173'.'%')
                        ->where('card_mask_no','NOT LIKE','8174'.'%')
                        ->sum('total_price');

                    $data['station_name']       = $stn_data ->stn_name;
                    $data['sv_collection']      = $cardFee       + $totalPrice;
                    $data['ppbl_sv_revenue']    = $ppbl_revenue  + $ppbl_stnPenAmount;
                    $data['sbi_sv_revenue']     = $sbi_revenue   + $sbi_stnPenAmount;
                    $data['other_sv_revenue']   = $other_revenue + $other_stnPenAmount;

                    $cardData[] = $data;

                } catch (PDOException $e) {
                    $data['success']    = false;
                    $cardData[]         = $data;
                }

            }

            return response([
                'status'                    => true,
                'total_SV_Revenue'          => $totalRevenue        + $penAmount,
                'ppbl_total_SV_Revenue'     => $ppblTotalRevenue    + $ppblPenAmount,
                'sbi_total_SV_Revenue'      => $sbiTotalRevenue     + $sbiPenAmount,
                'other_total_SV_Revenue'    => $otherTotalRevenue   + $otherPenAmount,
                'total_SV_Collection'       => $totalCollection,
                'data'                      => $cardData,
            ]);

        }

    }

}
