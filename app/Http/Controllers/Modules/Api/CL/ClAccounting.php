<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDOException;
use Illuminate\Support\Facades\DB;

class ClAccounting extends Controller
{
   
    function ClAccounting(Request $request)
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

            $pay_ref = null;
            $is_test = null;

            if(array_key_exists("pay_ref", $transaction))  $pay_ref = $transaction['pay_ref'];
            if(array_key_exists("is_test", $transaction))  $is_test = $transaction['is_test'];

            try {

                /* FOR SV ACCOUNTING */
                if ($transaction['product_id'] == 3) {

                    /* VALIDATION */
                    $validator = Validator::make($request->all(), [
                        '*.atek_id'       => 'required',
                        '*.txn_date'      => 'required',
                        '*.engraved_id'   => 'required',
                        '*.op_type_id'    => 'required|integer',
                        '*.stn_id'        => 'required|integer',
                        '*.cash_col'      => 'required',
                        '*.cash_ret'      => 'required',
                        '*.total_price'   => 'required',
                        '*.pre_chip_bal'  => 'required',
                        '*.pos_chip_bal'  => 'required',
                        '*.media_type_id' => 'required',
                        '*.product_id'    => 'required|integer',
                        '*.pass_id'       => 'required',
                        '*.pass_expiry'   => 'required',
                        '*.shift_id'      => 'required',
                        '*.user_id'       => 'required',
                        '*.eq_id'         => 'required',
                        '*.pay_type_id'   => 'required|integer',
                    ]);

                    /* IF VALIDATION FAILS */
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'error'  => str($validator->errors())
                        ]);
                    }

                    DB::table('cl_sv_accounting')->insert([
                        'atek_id'        => $transaction['atek_id'],
                        'txn_date'       => $transaction['txn_date'],
                        'engraved_id'    => $transaction['engraved_id'],
                        'op_type_id'     => $transaction['op_type_id'],
                        'stn_id'         => $transaction['stn_id'],
                        'cash_col'       => $transaction['cash_col'],
                        'cash_ret'       => $transaction['cash_ret'],
                        'total_price'    => $transaction['total_price'],
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

                }elseif ($transaction['product_id'] == 4) {

                    /* VALIDATION */
                    $validator = Validator::make($request->all(), [
                        '*.atek_id'         => 'required',
                        '*.txn_date'        => 'required',
                        '*.engraved_id'     => 'required',
                        '*.op_type_id'      => 'required|integer',
                        '*.stn_id'          => 'required|integer',
                        '*.cash_col'        => 'required',
                        '*.cash_ret'        => 'required',
                        '*.total_price'     => 'required',
                        '*.num_trips'       => 'required',
                        '*.rem_trips'       => 'required',
                        '*.media_type_id'   => 'required|integer',
                        '*.product_id'      => 'required|integer',
                        '*.pass_id'         => 'required',
                        '*.pass_expiry'     => 'required',
                        '*.src_stn_id'      => 'required|integer',
                        '*.des_stn_id'      => 'required',
                        '*.shift_id'        => 'required',
                        '*.user_id'         => 'required',
                        '*.eq_id'           => 'required',
                        '*.pay_type_id'     => 'required|integer',

                    ]);

                    /* IF VALIDATION FAILS */
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => false,
                            'error'  => str($validator->errors())
                        ]);
                    }

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

                }else{
                    return response([
                        'status' => false,
                        'error'  => "Invalid Product ID!"
                    ]);
                }

                $transData['is_settled'] = true;
                $transData['atek_id']    = $transaction['atek_id'];

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
            'status' => true,
            'trans'  => $response
        ]);

    }
}
