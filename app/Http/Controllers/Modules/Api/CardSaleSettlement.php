<?php

namespace App\Http\Controllers\Modules\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDOException;

/** While Card issuance from TOM data will
 * insert in open loop card sale for each card sale transaction **/
class CardSaleSettlement extends Controller{

    public function cardSale(Request $request){

        $transactions = json_decode($request->getContent());

        $response = [];

        foreach ($transactions as $transaction) {

            try {

                DB::table('ol_card_sale')->insert([
                    'atek_id'           => $transaction->atek_id, // AP0001<EQ_ID><TIMESTAMP>
                    'txn_date'          => $transaction->txn_date,
                    'bank_ref_id'       => $transaction->bank_ref_id,
                    'card_id'           => $transaction->card_id,
                    'card_mask_no'      => $transaction->card_mask_no,
                    'card_hash_no'      => $transaction->card_hash_no,
                    'card_type'         => $transaction->card_type,
                    'pay_scheme'        => $transaction->pay_scheme,
                    'op_type_id'        => $transaction->op_type_id,
                    'stn_id'            => $transaction->stn_id,
                    'cash_col'          => $transaction->cash_col,
                    'cash_ret'          => $transaction->cash_ret,
                    'card_sec'          => $transaction->card_sec,
                    'total_price'       => $transaction->total_price,
                    'card_fee'          => $transaction->card_fee,
                    'initial_topup_amt' => $transaction->initial_topup_amt,
                    'pax_first_name'    => $transaction->pax_first_name,
                    'pax_middle_name'   => $transaction->pax_middle_name,
                    'pax_last_name'     => $transaction->pax_last_name,
                    'pax_mobile'        => $transaction->pax_mobile,
                    'pax_gen_type'      => $transaction->pax_gen_type,
                    'shift_id'          => $transaction->shift_id,
                    'user_id'           => $transaction->user_id,
                    'eq_id'             => $transaction->eq_id,
                    'pay_type_id'       => $transaction->pay_type_id,
                    'pay_ref'           => $transaction->pay_ref,
                    'bank_id'           => $transaction->bank_id,
                    'card_iss_status'   => $transaction->card_iss_status, // 1.Card Issued ,2.Initial Topup Success
                    'created_at'        => Carbon::now(),
                ]);

                $response[] = [
                    'is_settled' => true,
                    'atek_id' => $transaction->atek_id,
                    'code' => 100
                ];

            } catch (PDOException $e) {

                $response[] = [
                    'is_settled' => false,
                    'atek_id' => $transaction->atek_id,
                    'code' => 101,
                    'error' => $e->getMessage()
                ];

            }

        }

        return response([
            'status' => true,
            'trans' => $response
        ]);

    }
}




