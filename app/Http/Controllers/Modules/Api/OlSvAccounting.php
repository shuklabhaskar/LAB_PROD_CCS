<?php

namespace App\Http\Controllers\Modules\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class OlSvAccounting extends Controller
{
    public function OlSvAccounting(Request $request)
    {
        /* CONVERTING REQUESTED DATA IN FORMAT */
        $data = $request->getContent();

        $dataArray = json_decode($data);
        $trans = [];

        /* VALIDATION */
        $validator = Validator::make($request->all(), [
            '*.atek_id'       => 'required',
        ]);

        /* IF VALIDATION FAILS */
       if ($validator->fails()) {
            return response()->json([
                'status'    => true,
                'error'     => $validator->errors()
            ]);
        } else {
        foreach ($dataArray as $transaction) {


            if (isset($transaction->sale_atek_id)) {

                DB::table("ol_card_sale")
                    ->where('atek_id', '=', $transaction->sale_atek_id)
                    ->update([
                        'card_mask_no'      => $transaction->card_mask_no,
                        'card_hash_no'      => $transaction->card_hash_no,
                        'card_iss_status'   => 3,
                        'updated_at'        => Carbon::now()
                    ]);
            }

            $transData = [];

            try {

                DB::table("ol_sv_accounting")->insert([
                    'atek_id'       => $transaction->atek_id,
                    'txn_date'      => $transaction->txn_date,
                    'bank_ref_id'   => isset($transaction->bank_ref_id) ? $transaction->bank_ref_id : "",
                    'card_mask_no'  => $transaction->card_mask_no,
                    'card_hash_no'  => $transaction->card_hash_no,
                    'pay_scheme'    => $transaction->pay_scheme,
                    'op_type_id'    => $transaction->op_type_id,
                    'stn_id'        => $transaction->stn_id,
                    'cash_col'      => $transaction->cash_col,
                    'cash_ret'      => $transaction->cash_ret,
                    'total_price'   => $transaction->total_price,
                    'pre_chip_bal'  => $transaction->pre_chip_bal,
                    'pos_chip_bal'  => $transaction->pos_chip_bal,
                    'media_type_id' => $transaction->media_type_id,
                    'product_id'    => $transaction->product_id,
                    'pass_id'       => $transaction->pass_id,
                    'shift_id'      => $transaction->shift_id,
                    'user_id'       => $transaction->user_id,
                    'eq_id'         => $transaction->eq_id,
                    'merchant_id'   => $transaction->merchant_id,
                    'terminal_id'   => $transaction->terminal_id,
                    'pay_type_id'   => $transaction->pay_type_id,
                    'pay_ref'       => $transaction->pay_ref,
                    'created_at'    => Carbon::now(),
                ]);

                $msAccID = DB::table("ol_sv_accounting")
                    ->where('atek_id','=',$transaction->atek_id)
                    ->select('ms_acc_id')
                    ->first();


                /* FOR OVER TRAVEL OP TYPE ID 54 */
                if ($transaction->op_type_id==54){

                    DB::table("ol_pen_accounting")->insert([
                        'ms_acc_id'         => $msAccID->ms_acc_id,
                        'txn_date'          => $transaction->txn_date,
                        'card_mask_no'      => $transaction->card_mask_no,
                        'card_hash_no'      => $transaction->card_hash_no,
                        'pay_scheme'        => $transaction->pay_scheme,
                        'pen_type_id'       => 24,
                        'pen_price'         => $transaction->total_price,
                        'stn_id'            => $transaction->stn_id,
                        'media_type_id'     => $transaction->media_type_id,
                        'product_id'        => $transaction->product_id,
                        'pass_id'           => $transaction->pass_id,
                        'created_at'        => Carbon::now(),
                    ]);
                }

                /* FOR EXCESS TIME SAME STATION OP TYPE ID 61 */
                if ($transaction->op_type_id==61){

                    DB::table("ol_pen_accounting")->insert([
                        'ms_acc_id'         => $msAccID->ms_acc_id,
                        'txn_date'          => $transaction->txn_date,
                        'card_mask_no'      => $transaction->card_mask_no,
                        'card_hash_no'      => $transaction->card_hash_no,
                        'pay_scheme'        => $transaction->pay_scheme,
                        'pen_type_id'       => 31,
                        'pen_price'         => $transaction->total_price,
                        'stn_id'            => $transaction->stn_id,
                        'media_type_id'     => $transaction->media_type_id,
                        'product_id'        => $transaction->product_id,
                        'pass_id'           => $transaction->pass_id,
                        'created_at'        => Carbon::now(),
                    ]);
                }

                /* FOR EXCESS TIME EXIT STATION OP TYPE ID 62 */
                if ($transaction->op_type_id==62){

                    DB::table("ol_pen_accounting")->insert([
                        'ms_acc_id'         => $msAccID->ms_acc_id,
                        'txn_date'          => $transaction->txn_date,
                        'card_mask_no'      => $transaction->card_mask_no,
                        'card_hash_no'      => $transaction->card_hash_no,
                        'pay_scheme'        => $transaction->pay_scheme,
                        'pen_type_id'       => 32,
                        'pen_price'         => $transaction->total_price,
                        'stn_id'            => $transaction->stn_id,
                        'media_type_id'     => $transaction->media_type_id,
                        'product_id'        => $transaction->product_id,
                        'pass_id'           => $transaction->pass_id,
                        'created_at'        => Carbon::now(),
                    ]);
                }

                /* FOR ENTRY MISMATCH SAME TIME OP TYPE ID 63 */
                if ($transaction->op_type_id==63){

                    DB::table("ol_pen_accounting")->insert([
                        'ms_acc_id'         => $msAccID->ms_acc_id,
                        'txn_date'          => $transaction->txn_date,
                        'card_mask_no'      => $transaction->card_mask_no,
                        'card_hash_no'      => $transaction->card_hash_no,
                        'pay_scheme'        => $transaction->pay_scheme,
                        'pen_type_id'       => 33,
                        'pen_price'         => $transaction->total_price,
                        'stn_id'            => $transaction->stn_id,
                        'media_type_id'     => $transaction->media_type_id,
                        'product_id'        => $transaction->product_id,
                        'pass_id'           => $transaction->pass_id,
                        'created_at'        => Carbon::now(),
                    ]);
                }

                /* FOR ENTRY MISMATCH NO EXIT OP TYPE ID 64 */
                if ($transaction->op_type_id==64){

                    DB::table("ol_pen_accounting")->insert([
                        'ms_acc_id'         => $msAccID->ms_acc_id,
                        'txn_date'          => $transaction->txn_date,
                        'card_mask_no'      => $transaction->card_mask_no,
                        'card_hash_no'      => $transaction->card_hash_no,
                        'pay_scheme'        => $transaction->pay_scheme,
                        'pen_type_id'       => 34,
                        'pen_price'         => $transaction->total_price,
                        'stn_id'            => $transaction->stn_id,
                        'media_type_id'     => $transaction->media_type_id,
                        'product_id'        => $transaction->product_id,
                        'pass_id'           => $transaction->pass_id,
                        'created_at'        => Carbon::now(),
                    ]);
                }

                /* FOR EXIT MISMATCH NO ENTRY OP TYPE ID 65 */
                if ($transaction->op_type_id==65){

                    DB::table("ol_pen_accounting")->insert([
                        'ms_acc_id'         => $msAccID->ms_acc_id,
                        'txn_date'          => $transaction->txn_date,
                        'card_mask_no'      => $transaction->card_mask_no,
                        'card_hash_no'      => $transaction->card_hash_no,
                        'pay_scheme'        => $transaction->pay_scheme,
                        'pen_type_id'       => 35,
                        'pen_price'         => $transaction->total_price,
                        'stn_id'            => $transaction->stn_id,
                        'media_type_id'     => $transaction->media_type_id,
                        'product_id'        => $transaction->product_id,
                        'pass_id'           => $transaction->pass_id,
                        'created_at'        => Carbon::now(),
                    ]);
                }

                $transData['is_settled'] = true;
                $transData['atek_id'] = $transaction->atek_id;

                array_push($trans, $transData);

            } catch (PDOException $e) {


                $AtekID = DB::table('ol_sv_accounting')->where('atek_id','=',$transaction->atek_id)->first();

                if ($AtekID != null){
                    $transData['is_settled']    = true;
                    $transData['atek_id']       = $transaction->atek_id;
                    $transData['error']         = $e->getMessage();
                }else{
                    $transData['is_settled']    = false;
                    $transData['atek_id']       = $transaction->atek_id;
                    $transData['error']         = $e->getMessage();
                }

                array_push($trans, $transData);

            }

        }

            return response([
                    'status' => true,
                    'trans' => $trans,
                ]
            );

        }
    }
}
