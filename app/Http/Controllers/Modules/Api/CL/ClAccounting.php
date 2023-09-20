<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDOException;

class ClAccounting extends Controller
{
    public function ClAccounting(Request $request)
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

            $productID = $transaction['product_id'];

            if ($productID == 3) {

                $transData = $this->svAccounting($transaction);
                $response[] = $transData;

            } elseif ($productID == 4) {

                $transData = $this->tpAccounting($transaction);
                $response[] = $transData;

            } else {

                $transData['is_settled'] = false;
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = "Invalid Product Type ID !";

            }

        }

        return response([
            'status' => true,
            'trans' => $response
        ]);

    }

    public function svAccounting($transaction)
    {

        try {
            DB::table('cl_sv_accounting')->insert([
                'atek_id' => $transaction['atek_id'],
                'txn_date' => $transaction['txn_date'],
                'engraved_id' => $transaction['engraved_id'],
                'op_type_id' => $transaction['op_type_id'],
                'stn_id' => $transaction['stn_id'],
                'cash_col' => $transaction['cash_col'],
                'cash_ret' => $transaction['cash_ret'],
                'pass_price' => $transaction['pass_price'],
                'card_fee' => $transaction['card_fee'],
                'card_sec' => $transaction['card_sec'],
                'processing_fee' => $transaction['processing_fee'],
                'total_price' => $transaction['total_price'],
                'pass_ref_chr' => $transaction['pass_ref_chr'],
                'card_fee_ref_chr' => $transaction['card_fee_ref_chr'],
                'card_sec_ref_chr' => $transaction['card_sec_ref_chr'],
                'pre_chip_bal' => $transaction['pre_chip_bal'],
                'pos_chip_bal' => $transaction['pos_chip_bal'],
                'media_type_id' => $transaction['media_type_id'],
                'product_id' => $transaction['product_id'],
                'pass_id' => $transaction['pass_id'],
                'pass_expiry' => $transaction['pass_expiry'],
                'pax_first_name' => $transaction['pax_first_name'],
                'pax_last_name' => $transaction['pax_last_name'],
                'pax_mobile' => $transaction['pax_mobile'],
                'pax_gen_type' => $transaction['pax_gen_type'],
                'shift_id' => $transaction['shift_id'],
                'user_id' => $transaction['user_id'],
                'eq_id' => $transaction['eq_id'],
                'pay_type_id' => $transaction['pay_type_id'],
                'pay_ref' => $transaction['pay_ref'],
                'is_test' => $transaction['is_test'],
                'old_engraved_id' => $transaction['old_engraved_id'],
            ]);

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            /* IF COLUMN IDENTITY FOUND AS ERROR */
            if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                $transData['is_settled'] = true;
            } else {
                $transData['is_settled'] = false;
            }
            $transData['atek_id'] = $transaction['atek_id'];
            $transData['error'] = $e->getMessage();

            return $transData;

        }
    }

    public function tpAccounting($transaction)
    {

        try {

            DB::table('cl_tp_accounting')->insert([
                'atek_id' => $transaction['atek_id'],
                'txn_date' => $transaction['txn_date'],
                'engraved_id' => $transaction['engraved_id'],
                'op_type_id' => $transaction['op_type_id'],
                'stn_id' => $transaction['stn_id'],
                'cash_col' => $transaction['cash_col'],
                'cash_ret' => $transaction['cash_ret'],
                'pass_price' => $transaction['pass_price'],
                'card_fee' => $transaction['card_fee'],
                'card_sec' => $transaction['card_sec'],
                'processing_fee' => $transaction['processing_fee'],
                'total_price' => $transaction['total_price'],
                'pass_ref_chr' => $transaction['pass_ref_chr'],
                'card_fee_ref_chr' => $transaction['card_fee_ref_chr'],
                'card_sec_ref_chr' => $transaction['card_sec_ref_chr'],
                'num_trips' => $transaction['num_trips'],
                'rem_trips' => $transaction['rem_trips'],
                'media_type_id' => $transaction['media_type_id'],
                'product_id' => $transaction['product_id'],
                'pass_id' => $transaction['pass_id'],
                'pass_expiry' => $transaction['pass_expiry'],
                'src_stn_id' => $transaction['src_stn_id'],
                'des_stn_id' => $transaction['des_stn_id'],
                'pax_first_name' => $transaction['pax_first_name'],
                'pax_last_name' => $transaction['pax_last_name'],
                'pax_mobile' => $transaction['pax_mobile'],
                'pax_gen_type' => $transaction['pax_gen_type'],
                'shift_id' => $transaction['shift_id'],
                'user_id' => $transaction['user_id'],
                'eq_id' => $transaction['eq_id'],
                'pay_type_id' => $transaction['pay_type_id'],
                'pay_ref' => $transaction['pay_ref'],
                'is_test' => $transaction['is_test'],
                'old_engraved_id' => $transaction['old_engraved_id'],
            ]);

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            /* IF COLUMN IDENTITY FOUND AS ERROR */
            if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                $transData['is_settled'] = true;
            } else {
                $transData['is_settled'] = false;
            }
            $transData['atek_id'] = $transaction['atek_id'];
            $transData['error'] = $e->getMessage();

            return $transData;

        }

    }
}
