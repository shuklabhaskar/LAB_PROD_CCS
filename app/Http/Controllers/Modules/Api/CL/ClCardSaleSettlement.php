<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDOException;
use Illuminate\Support\Facades\DB;

class ClCardSaleSettlement extends Controller
{

    function clCardSale(Request $request)
    {
        $transactions = json_decode($request->getContent(), true);
        $response = [];

        if ($transactions == [] || $transactions == null)
            return response([
                'status' => false,
                'error' => "please provide required data!"
            ]);

        foreach ($transactions as $transaction) {

            $pay_ref                =null;
            $is_test                =null;
            $pax_last_name          =null;

            /* IF BELOW LISTED COLUMN FOUND NULL THAN */
            if(array_key_exists("pay_ref", $transaction))        $pay_ref       =$transaction['pay_ref'];
            if(array_key_exists("is_test", $transaction))        $is_test       =$transaction['is_test'];
            if(array_key_exists("pax_last_name", $transaction))  $pax_last_name =$transaction['pax_last_name'];

                try {

                    /* FOR STORE VALUE PASS ONLY */
                    if ($transaction['product_id'] == 3) {

                        $validator = Validator::make($request->all(), [
                            '*.atek_id'                 => 'required',
                            '*.txn_date'                => 'required',
                            '*.engraved_id'             => 'required',
                            '*.issue_op_type_id'        => 'required',
                            '*.stn_id'                  => 'required|integer',
                            '*.cash_col'                => 'required',
                            '*.cash_ret'                => 'required',
                            '*.media_type_id'           => 'required|integer',
                            '*.product_id'              => 'required|integer',
                            '*.pass_id'                 => 'required',
                            '*.pass_expiry'             => 'required',
                            '*.shift_id'                => 'required',
                            '*.user_id'                 => 'required',
                            '*.eq_id'                   => 'required',
                            '*.pay_type_id'             => 'required',
                            '*.pre_chip_bal'            => 'required',
                            '*.pos_chip_bal'            => 'required',
                            '*.card_fee'                => 'required',
                            '*.card_sec'                => 'required',
                            '*.pax_first_name'          => 'required',
                            '*.pax_mobile'              => 'required|max_digits:10|min_digits:10',
                            '*.pax_gen_type'            => 'required',
                            '*.card_type_id'            => 'required|integer',
                            '*.top_up_op_type_id'       => 'required|integer',
                            '*.initial_top_up_amount'   => 'required',
                        ]);

                        if ($validator->fails()) return response()->json([
                            'status' => true,
                            'error' => str($validator->errors())
                        ]);

                        DB::table('cl_card_sale')->insert([
                            'atek_id'         => $transaction['atek_id'],
                            'txn_date'        => $transaction['txn_date'],
                            'engraved_id'     => $transaction['engraved_id'],
                            'op_type_id'      => $transaction['issue_op_type_id'],
                            'stn_id'          => $transaction['stn_id'],
                            'total_price'     => $transaction['card_fee']  + $transaction['card_sec'],   //TOTAL = CARD FEE + SEC DEPODIT
                            'card_fee'        => $transaction['card_fee'],
                            'card_sec'        => $transaction['card_sec'],
                            'pax_first_name'  => $transaction['pax_first_name'],
                            'pax_last_name'   => $pax_last_name,
                            'pax_mobile'      => $transaction['pax_mobile'],
                            'pax_gen_type'    => $transaction['pax_gen_type'],
                            'shift_id'        => $transaction['shift_id'],
                            'user_id'         => $transaction['user_id'],
                            'eq_id'           => $transaction['eq_id'],
                            'pay_type_id'     => $transaction['pay_type_id'],
                            'pay_ref'         => $pay_ref,
                            'is_test'         => $is_test,
                            'media_type_id'   => $transaction['media_type_id'],
                            'card_type_id'    => $transaction['card_type_id'],
                        ]);

                        DB::table('cl_sv_accounting')->insert([
                            'atek_id'           => $transaction['atek_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'engraved_id'       => $transaction['engraved_id'],
                            'op_type_id'        => $transaction['top_up_op_type_id'],  // TOP_UP_OP_TYPE_ID
                            'stn_id'            => $transaction['stn_id'],
                            'cash_col'          => $transaction['cash_col'],
                            'cash_ret'          => $transaction['cash_ret'],
                            'total_price'       => $transaction['initial_top_up_amount'],  // INITIAL_TOP_UP_AMOUNT
                            'pre_chip_bal'      => $transaction['pre_chip_bal'],
                            'pos_chip_bal'      => $transaction['pos_chip_bal'],
                            'media_type_id'     => $transaction['media_type_id'],
                            'product_id'        => $transaction['product_id'],
                            'pass_id'           => $transaction['pass_id'],
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'shift_id'          => $transaction['shift_id'],
                            'user_id'           => $transaction['user_id'],
                            'eq_id'             => $transaction['eq_id'],
                            'pay_type_id'       => $transaction['pay_type_id'],
                            'pay_ref'           => $pay_ref,
                            'is_test'           => $is_test,
                        ]);

                        /* FOR TRIP PASS ONLY */
                    }elseif  ($transaction['product_id'] == 4) {

                        $validator = Validator::make($request->all(), [
                            '*.atek_id'                 => 'required',
                            '*.txn_date'                => 'required',
                            '*.engraved_id'             => 'required',
                            '*.issue_op_type_id'        => 'required|integer',
                            '*.stn_id'                  => 'required|integer',
                            '*.cash_col'                => 'required',
                            '*.cash_ret'                => 'required',
                            '*.num_trips'               => 'required',
                            '*.rem_trips'               => 'required',
                            '*.media_type_id'           => 'required|integer',
                            '*.product_id'              => 'required|integer',
                            '*.pass_id'                 => 'required',
                            '*.pass_expiry'             => 'required',
                            '*.src_stn_id'              => 'required',
                            '*.des_stn_id'              => 'required',
                            '*.shift_id'                => 'required',
                            '*.user_id'                 => 'required',
                            '*.eq_id'                   => 'required',
                            '*.pay_type_id'             => 'required',
                            '*.pre_chip_bal'            => 'required',
                            '*.pos_chip_bal'            => 'required',
                            '*.card_fee'                => 'required',
                            '*.card_sec'                => 'required',
                            '*.pax_first_name'          => 'required',
                            '*.pax_mobile'              => 'required|max_digits:10|min_digits:10',
                            '*.pax_gen_type'            => 'required',
                            '*.card_type_id'            => 'required|integer',
                            '*.top_up_op_type_id'       => 'required|integer',
                            '*.initial_top_up_amount'   => 'required',
                        ]);

                        if ($validator->fails()) return response()->json([
                            'status' => true,
                            'error' => str($validator->errors())
                        ]);

                        DB::table('cl_card_sale')->insert([
                            'atek_id'           => $transaction['atek_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'engraved_id'       => $transaction['engraved_id'],
                            'op_type_id'        => $transaction['issue_op_type_id'], // ISSUE OP TYPE ID
                            'stn_id'            => $transaction['stn_id'],
                            'total_price'       => $transaction['card_fee'] + $transaction['card_sec'], // TOTAL = CARD FEE + SEC DEPODIT
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $transaction['card_sec'],
                            'pax_first_name'    => $transaction['pax_first_name'],
                            'pax_last_name'     => $pax_last_name,
                            'pax_mobile'        => $transaction['pax_mobile'],
                            'pax_gen_type'      => $transaction['pax_gen_type'],
                            'shift_id'          => $transaction['shift_id'],
                            'user_id'           => $transaction['user_id'],
                            'eq_id'             => $transaction['eq_id'],
                            'pay_type_id'       => $transaction['pay_type_id'],
                            'pay_ref'           => $pay_ref,
                            'is_test'           => $is_test,
                            'media_type_id'     => $transaction['media_type_id'],
                            'card_type_id'      => $transaction['card_type_id'],
                        ]);

                        DB::table('cl_tp_accounting')->insert([
                            'atek_id'       => $transaction['atek_id'],
                            'txn_date'      => $transaction['txn_date'],
                            'engraved_id'   => $transaction['engraved_id'],
                            'op_type_id'    => $transaction['top_up_op_type_id'],  // TOP UP OP TYPE ID
                            'stn_id'        => $transaction['stn_id'],
                            'cash_col'      => $transaction['cash_col'],
                            'cash_ret'      => $transaction['cash_ret'],
                            'total_price'   => $transaction['initial_top_up_amount'], // INITIAL TOP UP AMOUNT
                            'num_trips'     => $transaction['num_trips'],
                            'rem_trips'     => $transaction['rem_trips'],
                            'media_type_id' => $transaction['media_type_id'],
                            'product_id'    => $transaction['product_id'],
                            'pass_id'       => $transaction['pass_id'],
                            'pass_expiry'   => $transaction['pass_expiry'],
                            'src_stn_id'    => $transaction['src_stn_id'],
                            'des_stn_id'    => $transaction['des_stn_id'],
                            'shift_id'      => $transaction['shift_id'],
                            'user_id'       => $transaction['user_id'],
                            'eq_id'         => $transaction['eq_id'],
                            'pay_type_id'   => $transaction['pay_type_id'],
                            'pay_ref'       => $pay_ref,
                            'is_test'       => $is_test,
                        ]);

                    }else {
                            return response([
                                'status' => false,
                                'error'  => "Invalid Product ID!"
                            ]);
                        }

                    $transData['is_settled']    = true;
                    $transData['atek_id']       = $transaction['atek_id'];

                    array_push($response, $transData);

                } catch (PDOException $e) {

                    /* IF COLUMN IDENTITY FOUND AS ERROR */
                    if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                        $transData['is_settled']    = true;
                        $transData['atek_id']       = $transaction['atek_id'];
                        $transData['error']         = $e->getMessage();
                    } else {
                        $transData['is_settled']    = false;
                        $transData['atek_id']       = $transaction['atek_id'];
                        $transData['error']         = $e->getMessage();
                    }

                    array_push($response, $transData);

                }

        }

        return response([
            'status'    => true,
            'trans'     => $response
        ]);

    }


}