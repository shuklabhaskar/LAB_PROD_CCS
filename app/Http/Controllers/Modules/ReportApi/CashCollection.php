<?php

namespace App\Http\Controllers\Modules\ReportApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class CashCollection extends Controller
{
    public function index(Request $request)
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

            $cardData = [];

            /* TO GET NUMBER OF STATION AND STATION NAME */
            $stations = DB::table('station_inventory')->orderBy('stn_id','ASC')
                ->select('stn_name', 'stn_id')
                ->get();

            $ttlCardFee = DB::table('ol_card_sale as ocs')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->sum('total_price');

            $ttlTotalPrice = DB::table('ol_sv_accounting as osa')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->whereIn('pay_type_id', [1, 2, 3])
                ->sum('total_price');

            $ttlOnlineReloadAmount = DB::table('ol_sv_accounting as osa')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->whereIn('pay_type_id', [4,5])
                ->sum('total_price');

            /* NUMBER OF REFUND */
            $ttlRefund = DB::table('ol_sv_accounting')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->where('op_type_id', '=', 6)
                ->count();

            /*NUMBER OF TOP UP*/
            $topUpCount = DB::table('ol_sv_accounting')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->where('pass_id','=',82)
                ->where('op_type_id', '=', 3)
                ->count();

            $ttlSVIssued = DB::table('ol_card_sale')
                ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                ->count();

            $totalCollection = $ttlCardFee + $ttlTotalPrice;

            foreach ($stations as $stn_data) {

                try {

                    /* TOTAL CARD FEE */
                    $cadFee = DB::table('ol_card_sale as ocs')
                        ->where('ocs.stn_id', '=', $stn_data->stn_id)
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->sum('total_price');

                    /* ADDITION OF ALL TOTAL PRICE */
                    $totalPrice = DB::table('ol_sv_accounting as osa')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->where('osa.stn_id', '=', $stn_data->stn_id)
                        ->where('pass_id','=',82)
                        ->whereIn('pay_type_id', [1, 2, 3])
                        ->sum('total_price');

                    /* ADDITION OF ALL TOTAL PRICE FOR ONLINE RELOAD AMOUNT I.E 1.ACCOUNT 2.BALANCE UPDATE */
                    $onlineReloadAmount = DB::table('ol_sv_accounting as osa')
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->where('osa.stn_id', '=', $stn_data->stn_id)
                        ->where('pass_id','=',82)
                        ->whereIn('pay_type_id', [4,5])
                        ->sum('total_price');

                    /* NUMBER OF ISSUED CARD */
                    $cardIssued = DB::table('ol_card_sale')
                        ->where('stn_id', '=', $stn_data->stn_id)
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->count();

                    /* NUMBER OF TOP UP */
                    $topUpCount = DB::table('ol_sv_accounting')
                        ->where('stn_id', '=', $stn_data->stn_id)
                        ->where('pass_id','=',82)
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->where('op_type_id', '=', 3)
                        ->whereIn('pay_type_id', [1, 2, 3])
                        ->count();

                    /* NUMBER OF REFUND */
                    $refund = DB::table('ol_sv_accounting')
                        ->where('stn_id', '=', $stn_data->stn_id)
                        ->where('pass_id','=',82)
                        ->whereBetween(DB::raw('(txn_date)'), [$from, $to])
                        ->where('op_type_id', '=', 6)
                        ->count();

                    $data['station_name']           = $stn_data->stn_name;
                    $data['sv_collection']          = $cadFee + $totalPrice;
                    $data['sv_issued_count']        = $cardIssued;
                    $data['sv_topup_count']         = $topUpCount;
                    $data['online_reload_amount']   = $onlineReloadAmount;
                    $data['sv_refund_count']        = $refund;

                    array_push($cardData, $data);


                } catch (PDOException $e) {
                    $data['success'] = false;
                    array_push($cardData, $data);
                }

            }

            return response([
                'status'                        => true,
                'total_SV_Collection'           => $totalCollection,
                'total_SV_Issued_count'         => $ttlSVIssued,
                'total_SV_TopUp_Count'          => $topUpCount,
                'total_online_reload_amount'    => $ttlOnlineReloadAmount,
                'total_SV_Refund_Count'         => $ttlRefund,
                'data'                          => $cardData,
            ]);

        }
    }

}
