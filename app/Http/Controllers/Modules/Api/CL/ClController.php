<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDOException;
use Illuminate\Support\Facades\DB;

class ClController extends Controller
{
    function syncCl(Request $request)
    {
        $transactions = json_decode($request->getContent(), true);
        $response = [];

        if ($transactions == [] || $transactions == null) {
            return response([
                'status' => false,
                'message' => "please provide required data!"
            ]);
        }

        foreach ($transactions as $transaction) {

            $opTypeId = $transaction['op_type_id'];

            if ($opTypeId == 11) { // ISSUANCE
                $transData = $this->processIssuance($transaction);
                array_push($response, $transData);
            } elseif ($opTypeId == 3) { // TOP UP
                $transData = $this->processTopUp($transaction);
                array_push($response, $transData);
            } elseif ($opTypeId == 6) { // REFUND
                $transData = $this->processRefund($transaction);
                array_push($response, $transData);
            } else {
                $transData['is_settled'] = false;
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = "Invalid Operation !";
            }

        }

        return response([
            'status' => true,
            'trans' => $response
        ]);

    }

    /* ISSUANCE PROCESS */
    private function processIssuance($transaction)
    {
        $pay_ref       = null;
        $is_test       = null;
        $pax_last_name = null;

        /* IF BELOW LISTED COLUMN FOUND NULL THAN */
        if(array_key_exists("pay_ref", $transaction))        $pay_ref       =   $transaction['pay_ref'];
        if(array_key_exists("is_test", $transaction))        $is_test       =   $transaction['is_test'];
        if(array_key_exists("pax_last_name", $transaction))  $pax_last_name =   $transaction['pax_last_name'];

        try {

            /* FOR STORE VALUE PASS ONLY */
            if ($transaction['product_id'] == 3) {

                DB::table('cl_card_sale')->insert([
                    'atek_id'         => $transaction['atek_id'],
                    'txn_date'        => $transaction['txn_date'],
                    'engraved_id'     => $transaction['engraved_id'],
                    'op_type_id'      => $transaction['issue_op_type_id'],
                    'stn_id'          => $transaction['stn_id'],
                    'total_price'     => $transaction['card_fee']  + $transaction['card_sec'],   //TOTAL = CARD FEE + SEC DEPOSIT
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
                    'atek_id'        => $transaction['atek_id'],
                    'txn_date'       => $transaction['txn_date'],
                    'engraved_id'    => $transaction['engraved_id'],
                    'op_type_id'     => $transaction['top_up_op_type_id'],  // TOP_UP_OP_TYPE_ID
                    'stn_id'         => $transaction['stn_id'],
                    'cash_col'       => $transaction['cash_col'],
                    'cash_ret'       => $transaction['cash_ret'],
                    'total_price'    => $transaction['initial_top_up_amount'],  // INITIAL_TOP_UP_AMOUNT
                    'pre_chip_bal'   => $transaction['pre_chip_bal'],
                    'pos_chip_bal'   => $transaction['pos_chip_bal'],
                    'media_type_id'  => $transaction['media_type_id'],
                    'product_id'     => $transaction['product_id'],
                    'pass_id'        => $transaction['pass_id'],
                    'pass_expiry'    => $transaction['pass_expiry'],
                    'shift_id'       => $transaction['shift_id'],
                    'user_id'        => $transaction['user_id'],
                    'eq_id'          => $transaction['eq_id'],
                    'pay_type_id'    => $transaction['pay_type_id'],
                    'pay_ref'        => $pay_ref,
                    'is_test'        => $is_test,
                ]);

                /* FOR TRIP PASS ONLY */
            } elseif  ($transaction['product_id'] == 4) {

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

            } else {
                $transData['is_settled']    = false;
                $transData['atek_id']       = $transaction['atek_id'];
                $transData['error']       = "Invalid Product Type ID !";
                return $transData;
            }

            $transData['is_settled']    = true;
            $transData['atek_id']       = $transaction['atek_id'];
            return $transData;

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

            return $transData;

        }

    }

    /* TOP UP PROCESS */
    private function processTopUp($transaction)
    {

        $pay_ref = null;
        $is_test = null;

        if (array_key_exists("pay_ref", $transaction)) $pay_ref = $transaction['pay_ref'];
        if (array_key_exists("is_test", $transaction)) $is_test = $transaction['is_test'];

        try {

            /* FOR SV ACCOUNTING */
            if ($transaction['product_id'] == 3) {

                DB::table('cl_sv_accounting')->insert([
                    'atek_id'       => $transaction['atek_id'],
                    'txn_date'      => $transaction['txn_date'],
                    'engraved_id'   => $transaction['engraved_id'],
                    'op_type_id'    => $transaction['top_up_op_type_id'],
                    'stn_id'        => $transaction['stn_id'],
                    'cash_col'      => $transaction['cash_col'],
                    'cash_ret'      => $transaction['cash_ret'],
                    'total_price'   => $transaction['total_price'],
                    'pre_chip_bal'  => $transaction['pre_chip_bal'],
                    'pos_chip_bal'  => $transaction['pos_chip_bal'],
                    'media_type_id' => $transaction['media_type_id'],
                    'product_id'    => $transaction['product_id'],
                    'pass_id'       => $transaction['pass_id'],
                    'pass_expiry'   => $transaction['pass_expiry'],
                    'shift_id'      => $transaction['shift_id'],
                    'user_id'       => $transaction['user_id'],
                    'eq_id'         => $transaction['eq_id'],
                    'pay_type_id'   => $transaction['pay_type_id'],
                    'pay_ref'       => $pay_ref,
                    'is_test'       => $is_test,
                ]);

            } elseif ($transaction['product_id'] == 4) {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id' => $transaction['atek_id'],
                    'txn_date' => $transaction['txn_date'],
                    'engraved_id' => $transaction['engraved_id'],
                    'op_type_id' => $transaction['top_up_op_type_id'],
                    'stn_id' => $transaction['stn_id'],
                    'cash_col' => $transaction['cash_col'],
                    'cash_ret' => $transaction['cash_ret'],
                    'total_price' => $transaction['total_price'],
                    'num_trips' => $transaction['num_trips'],
                    'rem_trips' => $transaction['rem_trips'],
                    'media_type_id' => $transaction['media_type_id'],
                    'product_id' => $transaction['product_id'],
                    'pass_id' => $transaction['pass_id'],
                    'pass_expiry' => $transaction['pass_expiry'],
                    'src_stn_id' => $transaction['src_stn_id'],
                    'des_stn_id' => $transaction['des_stn_id'],
                    'shift_id' => $transaction['shift_id'],
                    'user_id' => $transaction['user_id'],
                    'eq_id' => $transaction['eq_id'],
                    'pay_type_id' => $transaction['pay_type_id'],
                    'pay_ref' => $pay_ref,
                    'is_test' => $is_test,
                ]);

            } else {
                $transData['is_settled']    = false;
                $transData['atek_id']       = $transaction['atek_id'];
                $transData['error']       = "Invalid Product Type ID !";
                return $transData;
            }

            $transData['is_settled']    = true;
            $transData['atek_id']       = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            /* IF COLUMN IDENTITY FOUND AS ERROR */
            if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                $transData['is_settled'] = true;
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();
            } else {
                $transData['is_settled'] = false;
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();
            }

            return $transData;

        }

    }

    /* REFUND PROCESS */
    private function processRefund($transaction)
    {
        $pay_ref        = null;
        $is_test        = null;
        $pax_last_name  = null;

        if (array_key_exists("pay_ref", $transaction)) $pay_ref = $transaction['pay_ref'];
        if (array_key_exists("is_test", $transaction)) $is_test = $transaction['is_test'];
        if (array_key_exists("pax_last_name", $transaction)) $pax_last_name = $transaction['pax_last_name'];

        try {

            /* FOR SV ACCOUNTING */
            if ($transaction['product_id'] == 3) {

                 DB::table('cl_sv_accounting')->insert([
                    'atek_id'       => $transaction['atek_id'],
                    'txn_date'      => $transaction['txn_date'],
                    'engraved_id'   => $transaction['engraved_id'],
                    'op_type_id'    => $transaction['op_type_id'],
                    'stn_id'        => $transaction['stn_id'],
                    'cash_col'      => $transaction['cash_col'],
                    'cash_ret'      => $transaction['cash_ret'],
                    'total_price'   => $transaction['refund_pass_amount'],
                    'pre_chip_bal'  => $transaction['pre_chip_bal'],
                    'pos_chip_bal'  => $transaction['pos_chip_bal'],
                    'media_type_id' => $transaction['media_type_id'],
                    'product_id'    => $transaction['product_id'],
                    'pass_id'       => $transaction['pass_id'],
                    'pass_expiry'   => $transaction['pass_expiry'],
                    'shift_id'      => $transaction['shift_id'],
                    'user_id'       => $transaction['user_id'],
                    'eq_id'         => $transaction['eq_id'],
                    'pay_type_id'   => $transaction['pay_type_id'],
                    'pay_ref'       => $pay_ref,
                    'is_test'       => $is_test,
                ]);

                 DB::table('cl_card_sale')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'total_price'       => $transaction['card_fee'] + $transaction['card_sec'],   //TOTAL = CARD FEE + SEC DEPOSIT
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

            } elseif ($transaction['product_id'] == 4) {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id'       => $transaction['atek_id'],
                    'txn_date'      => $transaction['txn_date'],
                    'engraved_id'   => $transaction['engraved_id'],
                    'op_type_id'    => $transaction['op_type_id'],
                    'stn_id'        => $transaction['stn_id'],
                    'cash_col'      => $transaction['cash_col'],
                    'cash_ret'      => $transaction['cash_ret'],
                    'total_price'   => $transaction['total_price'],
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

                DB::table('cl_card_sale')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 6,
                    'stn_id'            => $transaction['stn_id'],
                    'total_price'       => $transaction['card_fee'] + $transaction['card_sec'],   //TOTAL = CARD FEE + SEC DEPODIT
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

            } else {
                $transData['is_settled']    = false;
                $transData['atek_id']       = $transaction['atek_id'];
                $transData['error']         = "Invalid Product Type ID !";
                return $transData;
            }

            $transData['is_settled']    = true;
            $transData['atek_id']       = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            /* IF COLUMN IDENTITY FOUND AS ERROR */
            if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                $transData['is_settled'] = true;
                $transData['atek_id']    = $transaction['atek_id'];
                $transData['error']      = $e->getMessage();
            } else {
                $transData['is_settled'] = false;
                $transData['atek_id']    = $transaction['atek_id'];
                $transData['error']      = $e->getMessage();
            }

            return $transData;

        }



    }

}
