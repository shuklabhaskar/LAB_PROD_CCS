<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;

class ClAccounting extends Controller
{
    public function ClAccounting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.atek_id'       => 'required',
            '*.des_stn_id'    => 'required|integer',
            '*.engraved_id'   => 'required',
            '*.eq_id'         => 'required|string',
            '*.media_type_id' => 'required|integer',
            '*.pass_id'       => 'required',
            '*.src_stn_id'    => 'required|integer',
            '*.stn_id'        => 'required|integer',
            '*.txn_date'      => 'required'
        ]);

        if ($validator->fails()) {
            return response([
                'status' => false,
                'error'  => json_encode($validator->errors())
            ]);
        }

        $transactions = json_decode($request->getContent(), true);
        $response     = [];

        /**
         * Sorts the transactions array in ascending order
         * based on the 'id' key as per the request.
         */
        usort($transactions, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });


        if ($transactions == [] || $transactions == null) {

            return response([
                'status'  => false,
                'message' => "please provide required data!"
            ]);

        }

        foreach ($transactions as $transaction) {

            $clStatus = DB::table('cl_status')
                ->where('engraved_id', '=', $transaction['engraved_id'])
                ->first();

            $opTypeId = $transaction['op_type_id'];

            $engravedIdExists = DB::table('cl_status')
                ->where('engraved_id', '=', $transaction['engraved_id'])
                ->value('engraved_id');

            if ($opTypeId == 1) { //ISSUANCE

                $transData  = $this->Issuance($transaction, $engravedIdExists);
                $response[] = $transData;

            } elseif ($opTypeId == 3) { // RELOAD

                $transData  = $this->Reload($transaction, $clStatus);
                $response[] = $transData;

            } elseif ($opTypeId == 6) { // REFUND

                $transData  = $this->Refund($transaction, $engravedIdExists);
                $response[] = $transData;

            } elseif ($opTypeId == 11) {

                /**
                 * REPLACEMENT IN CASE OF CARD NOT OKAY & PHYSICALLY OKAY
                 **/

                $transData  = $this->cardNotReadablePhysicallyOkay($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 12) { // REFUND

                /**
                 * REPLACEMENT IN CASE OF CARD NOT OKAY & PHYSICALLY NOT OKAY
                 **/

                $transData  = $this->cardNotReadablePhysicallyNotOkay($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 13) { // REFUND

                /** LOST CARD **/
                $transData  = $this->lostCard($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 54) {

                /** overTravel **/
                $transData  = $this->overTravel($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 61) {

                $transData  = $this->excessTimeSameStation($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 62) {

                $transData  = $this->excessTimeExitStation($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 63) {

                $transData  = $this->entryMismatchSameTime($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 64) {

                /** Entry Mismatch - Other Time (No Exit) **/
                $transData  = $this->entryMismatchOtherTime($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 65) {

                /** Exit Mismatch - Same Time **/
                $transData  = $this->exitMismatchNoExit($transaction);
                $response[] = $transData;

            } elseif ($opTypeId == 66) {

                /** Exit Mismatch - Other Time (No Entry) **/
                $transData  = $this->exitMismatchNoEntry($transaction);
                $response[] = $transData;

            } else {

                $transData['is_settled']    = false;
                $transData['atek_id']       = $transaction['atek_id'];
                $transData['error']         = "Invalid Product Type ID !";

            }

        }

        return response([
            'status' => true,
            'trans'  => $response
        ]);

    }

    public function Issuance($transaction, $engravedIdExists)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName    = "";
        $paxLastName     = "";
        $paxMobile       = 123456789;
        $paxGenType      = 0;
        $autoTopUpStatus = false;
        $autoTopUpAmount = 0.0;
        $bonusPoints     = 0.0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType = $transaction['pax_gen_type'];
        if (array_key_exists("auto_topup_status", $transaction)) $autoTopUpStatus = $transaction['auto_topup_status'];
        if (array_key_exists("auto_topup_amt", $transaction)) $autoTopUpAmount = $transaction['auto_topup_amt'];
        if (array_key_exists("bonus_points", $transaction)) $bonusPoints = $transaction['bonus_points'];

        try {

            if ($transaction['product_id'] == 3) {

                $svData = DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                if ($svData) {

                    if ($engravedIdExists) {
                        /* FOR CL SV ISSUANCE */

                        DB::table('cl_status')
                            ->where('engraved_id', '=', $transaction['engraved_id'])
                            ->update([
                                'engraved_id'        => $transaction['engraved_id'],
                                'chip_id'            => $transaction['chip_id'],
                                'txn_date'           => $transaction['txn_date'],
                                'pass_id'            => $transaction['pass_id'],
                                'product_id'         => $transaction['product_id'],
                                'card_fee'           => $transaction['card_fee'],
                                'card_sec'           => $transaction['card_sec'],
                                'sv_balance'         => $transaction['pos_chip_bal'],
                                'tp_balance'         => 0,
                                'pass_expiry'        => $transaction['pass_expiry'],
                                'src_stn_id'         => 0,
                                'des_stn_id'         => 0,
                                'auto_topup_status'  => $autoTopUpStatus,
                                'auto_topup_amt'     => $autoTopUpAmount,
                                'bonus_points'       => $bonusPoints,
                                'is_test'            => $transaction['is_test'],
                                'pax_first_name'     => $paxFirstName,
                                'pax_last_name'      => $paxLastName,
                                'pax_mobile'         => $paxMobile,
                                'pax_gen_type'       => $paxGenType,
                                'updated_at'         => now(),
                            ]);

                    }

                    if ($engravedIdExists == null) {

                        DB::table('cl_status')->insert([
                            'engraved_id'       => $transaction['engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $transaction['card_sec'],
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'src_stn_id'        => 0,
                            'des_stn_id'        => 0,
                            'tp_balance'        => 0,
                            'sv_balance'        => $transaction['pos_chip_bal'],
                            'auto_topup_status' => $autoTopUpStatus,
                            'auto_topup_amt'    => $autoTopUpAmount,
                            'bonus_points'      => $bonusPoints,
                            'is_test'           => $transaction['is_test'],
                            'pax_first_name'    => $paxFirstName,
                            'pax_last_name'     => $paxLastName,
                            'pax_mobile'        => $paxMobile,
                            'pax_gen_type'      => $paxGenType,
                        ]);

                    }

                }

            }

            if ($transaction['product_id'] == 4) {

                $TpData = DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                if ($TpData) {

                    if ($engravedIdExists) {

                        DB::table('cl_status')
                            ->where('engraved_id', '=', $transaction['engraved_id'])
                            ->update([
                                'engraved_id'       => $transaction['engraved_id'],
                                'chip_id'           => $transaction['chip_id'],
                                'txn_date'          => $transaction['txn_date'],
                                'pass_id'           => $transaction['pass_id'],
                                'product_id'        => $transaction['product_id'],
                                'card_fee'          => $transaction['card_fee'],
                                'card_sec'          => $transaction['card_sec'],
                                'tp_balance'        => $transaction['rem_trips'],
                                'sv_balance'        => 0,
                                'pass_expiry'       => $transaction['pass_expiry'],
                                'src_stn_id'        => $transaction['src_stn_id'],
                                'des_stn_id'        => $transaction['des_stn_id'],
                                'auto_topup_status' => $autoTopUpStatus,
                                'auto_topup_amt'    => $autoTopUpAmount,
                                'bonus_points'      => $bonusPoints,
                                'is_test'           => $transaction['is_test'],
                                'pax_first_name'    => $paxFirstName,
                                'pax_last_name'     => $paxLastName,
                                'pax_mobile'        => $paxMobile,
                                'pax_gen_type'      => $paxGenType,
                                'updated_at'        => now()
                            ]);
                    }

                    if ($engravedIdExists == null) {

                        DB::table('cl_status')->insert([
                            'engraved_id'       => $transaction['engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $transaction['card_sec'],
                            'tp_balance'        => $transaction['rem_trips'],
                            'sv_balance'        => 0,
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'src_stn_id'        => $transaction['src_stn_id'],
                            'des_stn_id'        => $transaction['des_stn_id'],
                            'auto_topup_status' => $autoTopUpStatus,
                            'auto_topup_amt'    => $autoTopUpAmount,
                            'bonus_points'      => $bonusPoints,
                            'is_test'           => $transaction['is_test'],
                            'pax_first_name'    => $paxFirstName,
                            'pax_last_name'     => $paxLastName,
                            'pax_mobile'        => $paxMobile,
                            'pax_gen_type'      => $paxGenType,
                        ]);

                    }
                }
            }

            $transData['is_settled'] = true;
            $transData['atek_id']    = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            /* IF COLUMN IDENTITY FOUND AS ERROR */
            if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                $transData['is_settled'] = true;
            } else {
                $transData['is_settled'] = false;
            }
            $transData['atek_id'] = $transaction['atek_id'];
            $transData['error']   = $e->getMessage();

            return $transData;

        }

    } /* OP_TYPE_ID = 1*/

    public function Reload($transaction)
    {
        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName = "";
        $paxLastName  = "";
        $paxMobile    = 123456789;
        $paxGenType   = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType = $transaction['pax_gen_type'];


        try {

            if ($transaction['product_id'] == 3) {

                DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]) ?: throw new PDOException("Failed to insert in cl_sv_accounting");

                $pass = DB::table("pass_inventory")
                    ->where('pass_id', '=', $transaction['pass_id'])
                    ->first() ?: throw new PDOException("Failed to found given Pass ID");

                $cardSecDeposit = DB::table('card_type')
                    ->where('card_type_id', '=', $pass->card_type_id)
                    ->first('card_sec') ?: throw new PDOException("Failed To Found Card Security");

                /*** MAKING
                 * TRIP PASS
                 * VARIABLES AS ZERO AND
                 * DOING TRANSACTION ONLY FOR STORE VALUE
                 */
                DB::table('cl_status')
                    ->where('engraved_id', '=', $transaction['engraved_id'])
                    ->update([
                        'txn_date'       => $transaction['txn_date'],
                        'sv_balance'     => $transaction['pos_chip_bal'],
                        'tp_balance'     => 0,
                        'pass_expiry'    => $transaction['pass_expiry'],
                        'pass_id'        => $transaction['pass_id'],
                        'product_id'     => $transaction['product_id'],
                        'pax_first_name' => $paxFirstName,
                        'pax_last_name'  => $paxLastName,
                        'pax_mobile'     => $paxMobile,
                        'card_sec'       => $cardSecDeposit->card_sec,
                        'src_stn_id'     => 0,
                        'des_stn_id'     => 0,
                        'updated_at'     => now(),
                    ]) ?: throw new PDOException("Failed to insert in cl_status");

            }

            if ($transaction['product_id'] == 4) {

                $tpData = DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]) ?: throw new PDOException("Failed to insert in cl_tp_accounting");


                $pass = DB::table("pass_inventory")
                    ->where('pass_id', '=', $transaction['pass_id'])
                    ->first() ?: throw new PDOException("Failed to found given Pass ID");

                $cardSecDeposit = DB::table('card_type')
                    ->where('card_type_id', '=', $pass->card_type_id)
                    ->first('card_sec') ?: throw new PDOException("Failed To Found Card Security");

                DB::table('cl_status')
                    ->where('engraved_id', '=', $transaction['engraved_id'])
                    ->update([
                        'txn_date'       => $transaction['txn_date'],
                        'tp_balance'     => $transaction['rem_trips'],
                        'sv_balance'     => 0,
                        'pass_id'        => $transaction['pass_id'],
                        'product_id'     => $transaction['product_id'],
                        'card_sec'       => $cardSecDeposit->card_sec,
                        'pass_expiry'    => $transaction['pass_expiry'],
                        'src_stn_id'     => $transaction['src_stn_id'],
                        'des_stn_id'     => $transaction['des_stn_id'],
                        'pax_first_name' => $paxFirstName,
                        'pax_last_name'  => $paxLastName,
                        'pax_mobile'     => $paxMobile,
                        'updated_at'     => now()
                    ])?: throw new PDOException("Failed to insert in cl_status");


            }

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            Log::channel("clAccounting")->error($e);

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


    } /* OP_TYPE_ID = 3*/

    public function Refund($transaction, $engravedIdExists)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName   = "";
        $paxLastName    = "";
        $paxMobile      = 123456789;
        $paxGenType     = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName   = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile        = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType     = $transaction['pax_gen_type'];

        try {

            if ($transaction['product_id'] == 3) {

                $svData = DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                if ($svData) {

                    if ($engravedIdExists) {

                        DB::table('cl_status')
                            ->where('engraved_id', '=', $transaction['engraved_id'])
                            ->update([
                                'engraved_id'       => $transaction['engraved_id'],
                                'chip_id'           => $transaction['chip_id'],
                                'txn_date'          => Carbon::now(),
                                'pass_id'           => 0,
                                'product_id'        => 0,
                                'card_fee'          => 0,
                                'card_sec'          => 0,
                                'sv_balance'        => 0,
                                'tp_balance'        => 0,
                                'pass_expiry'       => Carbon::now(),
                                'src_stn_id'        => 0,
                                'des_stn_id'        => 0,
                                'auto_topup_status' => false,
                                'auto_topup_amt'    => 0,
                                'bonus_points'      => 0,
                                'is_test'           => false,
                                'pax_first_name'    => "",
                                'pax_last_name'     => "",
                                'pax_mobile'        => 0000000000,
                                'pax_gen_type'      => 0,
                            ]);

                    }
                }

            }

            if ($transaction['product_id'] == 4) {

                $tpData = DB::table('cl_tp_accounting')->insert([
                    'atek_id'               => $transaction['atek_id'],
                    'txn_date'              => $transaction['txn_date'],
                    'engraved_id'           => $transaction['engraved_id'],
                    'op_type_id'            => $transaction['op_type_id'],
                    'stn_id'                => $transaction['stn_id'],
                    'cash_col'              => $transaction['cash_col'],
                    'cash_ret'              => $transaction['cash_ret'],
                    'pass_price'            => $transaction['pass_price'],
                    'card_fee'              => $transaction['card_fee'],
                    'card_sec'              => $transaction['card_sec'],
                    'processing_fee'        => $transaction['processing_fee'],
                    'total_price'           => $transaction['total_price'],
                    'pass_ref_chr'          => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'      => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'      => $transaction['card_sec_ref_chr'],
                    'num_trips'             => $transaction['num_trips'],
                    'rem_trips'             => $transaction['rem_trips'],
                    'media_type_id'         => $transaction['media_type_id'],
                    'product_id'            => $transaction['product_id'],
                    'pass_id'               => $transaction['pass_id'],
                    'pass_expiry'           => $transaction['pass_expiry'],
                    'src_stn_id'            => $transaction['src_stn_id'],
                    'des_stn_id'            => $transaction['des_stn_id'],
                    'pax_first_name'        => $paxFirstName,
                    'pax_last_name'         => $paxLastName,
                    'pax_mobile'            => $paxMobile,
                    'pax_gen_type'          => $paxGenType,
                    'shift_id'              => $transaction['shift_id'],
                    'user_id'               => $transaction['user_id'],
                    'eq_id'                 => $transaction['eq_id'],
                    'pay_type_id'           => $transaction['pay_type_id'],
                    'pay_ref'               => $transaction['pay_ref'],
                    'is_test'               => $transaction['is_test'],
                    'old_engraved_id'       => $transaction['old_engraved_id'],
                ]);

                if ($tpData) {
                    if ($engravedIdExists) {

                        DB::table('cl_status')
                            ->where('engraved_id', '=', $transaction['engraved_id'])
                            ->update([
                                'engraved_id'       => $transaction['engraved_id'],
                                'chip_id'           => $transaction['chip_id'],
                                'txn_date'          => Carbon::now(),
                                'pass_id'           => 0,
                                'product_id'        => 0,
                                'card_fee'          => 0,
                                'card_sec'          => 0,
                                'sv_balance'        => 0,
                                'tp_balance'        => 0,
                                'pass_expiry'       => Carbon::now(),
                                'src_stn_id'        => 0,
                                'des_stn_id'        => 0,
                                'auto_topup_status' => false,
                                'auto_topup_amt'    => 0,
                                'bonus_points'      => 0,
                                'is_test'           => false,
                                'pax_first_name'    => "",
                                'pax_last_name'     => "",
                                'pax_mobile'        => 0000000000,
                                'pax_gen_type'      => 0,
                            ]);

                    }
                }

            }

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

    } /* OP_TYPE_ID = 6*/

    public function cardNotReadablePhysicallyOkay($transaction)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName     = "";
        $paxLastName      = "";
        $paxMobile        = 123456789;
        $paxGenType       = 0;
        $autoTopUpStatus  = false;
        $autoTopUpAmount  = 0.0;
        $bonusPoints      = 0.0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName         = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName           = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile                = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType             = $transaction['pax_gen_type'];
        if (array_key_exists("auto_topup_status", $transaction)) $autoTopUpStatus   = $transaction['auto_topup_status'];
        if (array_key_exists("auto_topup_amt", $transaction)) $autoTopUpAmount      = $transaction['auto_topup_amt'];
        if (array_key_exists("bonus_points", $transaction)) $bonusPoints            = $transaction['bonus_points'];


        try {

            if ($transaction['product_id'] == 3) {

                $svData = DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                if ($svData) {

                    $pass = DB::table("pass_inventory")
                        ->where('pass_id', '=', $transaction['pass_id'])
                        ->first() ?: throw new PDOException("Failed to found given Pass ID");

                    $cardSecDeposit = DB::table('card_type')
                        ->where('card_type_id', '=', $pass->card_type_id)
                        ->first('card_sec') ?: throw new PDOException("Failed To Found Card Security");

                    DB::table('cl_status')
                        ->updateOrInsert(
                            ['engraved_id'      => $transaction['engraved_id']],
                            [
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $cardSecDeposit->card_sec,
                            'sv_balance'        => $transaction['pos_chip_bal'],
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'src_stn_id'        => 0,
                            'des_stn_id'        => 0,
                            'auto_topup_status' => $autoTopUpStatus,
                            'auto_topup_amt'    => $autoTopUpAmount,
                            'bonus_points'      => $bonusPoints,
                            'is_test'           => $transaction['is_test'],
                            'pax_first_name'    => $paxFirstName,
                            'pax_last_name'     => $paxLastName,
                            'pax_mobile'        => $paxMobile,
                            'pax_gen_type'      => $paxGenType,
                            'updated_at'        => now(),
                        ]);

                    $oldData = DB::table('cl_sn_mapping')
                        ->where('engraved_id','=',$transaction['old_engraved_id'])
                        ->first();

                    DB::table('cl_blacklist')->insert([
                        'ms_blk_reason_id'  => 3,
                        'start_date'        => Carbon::now(),
                        'engraved_id'       => $oldData->engraved_id,
                        'chip_id'           => $oldData->chip_id,
                    ]);

                }

            }

            if ($transaction['product_id'] == 4) {

                $tpData = DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);


                if ($tpData) {

                    $pass = DB::table("pass_inventory")
                        ->where('pass_id', '=', $transaction['pass_id'])
                        ->first() ?: throw new PDOException("Failed to found given Pass ID");

                    $cardSecDeposit = DB::table('card_type')
                        ->where('card_type_id', '=', $pass->card_type_id)
                        ->first('card_sec') ?: throw new PDOException("Failed To Found Card Security");


                    DB::table('cl_status')
                        ->updateOrInsert(
                            ['engraved_id'      => $transaction['engraved_id']],
                            [
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $cardSecDeposit->card_sec,
                            'tp_balance'        => $transaction['rem_trips'],
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'src_stn_id'        => $transaction['src_stn_id'],
                            'des_stn_id'        => $transaction['des_stn_id'],
                            'auto_topup_status' => $autoTopUpStatus,
                            'auto_topup_amt'    => $autoTopUpAmount,
                            'bonus_points'      => $bonusPoints,
                            'is_test'           => $transaction['is_test'],
                            'pax_first_name'    => $paxFirstName,
                            'pax_last_name'     => $paxLastName,
                            'pax_mobile'        => $paxMobile,
                            'pax_gen_type'      => $paxGenType,
                            'updated_at'        => now()
                        ]);

                    $oldData = DB::table('cl_sn_mapping')
                        ->where('engraved_id','=',$transaction['old_engraved_id'])
                        ->first();

                    DB::table('cl_blacklist')->insert([
                        'ms_blk_reason_id'  => 3,
                        'start_date'        => Carbon::now(),
                        'engraved_id'       => $oldData->engraved_id,
                        'chip_id'           => $oldData->chip_id,
                    ]);

                }

            }

            $transData['is_settled'] = true;
            $transData['atek_id']    = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            Log::channel('ClAccounting')->error($e);
            /* IF COLUMN IDENTITY FOUND AS ERROR */
            if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                $transData['is_settled'] = true;
            } else {
                $transData['is_settled'] = false;
            }
            $transData['atek_id']   = $transaction['atek_id'];
            $transData['error']     = $e->getMessage();

            return $transData;

        }
    } /* OP_TYPE_ID = 11*/

    public function cardNotReadablePhysicallyNotOkay($transaction)
    {
        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName       = "";
        $paxLastName        = "";
        $paxMobile          = 123456789;
        $autoTopUpStatus    = false;
        $paxGenType         = 0;
        $autoTopUpAmount    = 0.0;
        $bonusPoints        = 0.0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName         = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName           = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile                = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType             = $transaction['pax_gen_type'];
        if (array_key_exists("auto_topup_status", $transaction)) $autoTopUpStatus   = $transaction['auto_topup_status'];
        if (array_key_exists("auto_topup_amt", $transaction)) $autoTopUpAmount      = $transaction['auto_topup_amt'];
        if (array_key_exists("bonus_points", $transaction)) $bonusPoints            = $transaction['bonus_points'];

        try {

            if ($transaction['product_id'] == 3) {

                $svData = DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                if ($svData) {

                    $pass = DB::table("pass_inventory")
                        ->where('pass_id', '=', $transaction['pass_id'])
                        ->first() ?: throw new PDOException("Failed to found given Pass ID");

                    $cardSecDeposit = DB::table('card_type')
                        ->where('card_type_id', '=', $pass->card_type_id)
                        ->first('card_sec') ?: throw new PDOException("Failed To Found Card Security");

                    DB::table('cl_status')
                        ->updateOrInsert(
                            ['engraved_id'      => $transaction['engraved_id']],
                            [
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $cardSecDeposit->card_sec,
                            'sv_balance'        => $transaction['pos_chip_bal'],
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'src_stn_id'        => 0,
                            'des_stn_id'        => 0,
                            'auto_topup_status' => $autoTopUpStatus,
                            'auto_topup_amt'    => $autoTopUpAmount,
                            'bonus_points'      => $bonusPoints,
                            'is_test'           => $transaction['is_test'],
                            'pax_first_name'    => $paxFirstName,
                            'pax_last_name'     => $paxLastName,
                            'pax_mobile'        => $paxMobile,
                            'pax_gen_type'      => $paxGenType,
                            'updated_at'        => now(),
                        ]);

                    $oldData = DB::table('cl_sn_mapping')
                        ->where('engraved_id','=',$transaction['old_engraved_id'])
                        ->first();

                    DB::table('cl_blacklist')->insert([
                        'ms_blk_reason_id'  => 2,
                        'start_date'        => Carbon::now(),
                        'engraved_id'       => $oldData->engraved_id,
                        'chip_id'           => $oldData->chip_id,
                    ]);
                }

            }

            if ($transaction['product_id'] == 4) {

                $tpData = DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                if ($tpData) {

                    $pass = DB::table("pass_inventory")
                        ->where('pass_id', '=', $transaction['pass_id'])
                        ->first() ?: throw new PDOException("Failed to found given Pass ID");

                    $cardSecDeposit = DB::table('card_type')
                        ->where('card_type_id', '=', $pass->card_type_id)
                        ->first('card_sec') ?: throw new PDOException("Failed To Found Card Security");


                    DB::table('cl_status')
                        ->updateOrInsert(
                            ['engraved_id'      => $transaction['engraved_id']],
                            [
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $cardSecDeposit->card_sec,
                            'tp_balance'        => $transaction['rem_trips'],
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'src_stn_id'        => $transaction['src_stn_id'],
                            'des_stn_id'        => $transaction['des_stn_id'],
                            'auto_topup_status' => $autoTopUpStatus,
                            'auto_topup_amt'    => $autoTopUpAmount,
                            'bonus_points'      => $bonusPoints,
                            'is_test'           => $transaction['is_test'],
                            'pax_first_name'    => $paxFirstName,
                            'pax_last_name'     => $paxLastName,
                            'pax_mobile'        => $paxMobile,
                            'pax_gen_type'      => $paxGenType,
                            'updated_at'        => now()

                        ]);

                    $oldData = DB::table('cl_sn_mapping')
                        ->where('engraved_id','=',$transaction['old_engraved_id'])
                        ->first();

                    DB::table('cl_blacklist')->insert([
                        'ms_blk_reason_id'  => 2,
                        'start_date'        => Carbon::now(),
                        'engraved_id'       => $oldData->engraved_id,
                        'chip_id'           => $oldData->chip_id,
                    ]);

                }

            }

            $transData['is_settled'] = true;
            $transData['atek_id']    = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            Log::channel('ClAccounting')->error($e);
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
    } /* OP_TYPE_ID = 12*/

    public function lostCard($transaction)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName       = "";
        $paxLastName        = "";
        $paxMobile          = 123456789;
        $paxGenType         = 0;
        $autoTopUpStatus    = false;
        $autoTopUpAmount    = 0.0;
        $bonusPoints        = 0.0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName         = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName           = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile                = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType             = $transaction['pax_gen_type'];
        if (array_key_exists("auto_topup_status", $transaction)) $autoTopUpStatus   = $transaction['auto_topup_status'];
        if (array_key_exists("auto_topup_amt", $transaction)) $autoTopUpAmount      = $transaction['auto_topup_amt'];
        if (array_key_exists("bonus_points", $transaction)) $bonusPoints            = $transaction['bonus_points'];

        try {

            if ($transaction['product_id'] == 3) {

                $svData = DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                if ($svData) {

                    $pass = DB::table("pass_inventory")
                        ->where('pass_id', '=', $transaction['pass_id'])
                        ->first() ?: throw new PDOException("Failed to found given Pass ID");

                    $cardSecDeposit = DB::table('card_type')
                        ->where('card_type_id', '=', $pass->card_type_id)
                        ->first('card_sec') ?: throw new PDOException("Failed To Found Card Security");


                    DB::table('cl_status')
                        ->updateOrInsert(
                            ['engraved_id'      => $transaction['engraved_id']],
                            [
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $cardSecDeposit->card_sec,
                            'sv_balance'        => $transaction['pos_chip_bal'],
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'src_stn_id'        => 0,
                            'des_stn_id'        => 0,
                            'auto_topup_status' => $autoTopUpStatus,
                            'auto_topup_amt'    => $autoTopUpAmount,
                            'bonus_points'      => $bonusPoints,
                            'is_test'           => $transaction['is_test'],
                            'pax_first_name'    => $paxFirstName,
                            'pax_last_name'     => $paxLastName,
                            'pax_mobile'        => $paxMobile,
                            'pax_gen_type'      => $paxGenType,
                            'updated_at'        => now(),
                        ]);

                    $oldData = DB::table('cl_sn_mapping')
                        ->where('engraved_id','=',$transaction['old_engraved_id'])
                        ->first();

                    DB::table('cl_blacklist')->insert([
                        'ms_blk_reason_id'  => 1,
                        'start_date'        => Carbon::now(),
                        'engraved_id'       => $oldData->engraved_id,
                        'chip_id'           => $oldData->chip_id,
                    ]);

                }

            }

            if ($transaction['product_id'] == 4) {

                $tpData = DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                if ($tpData) {

                    $pass = DB::table("pass_inventory")
                        ->where('pass_id', '=', $transaction['pass_id'])
                        ->first() ?: throw new PDOException("Failed to found given Pass ID");

                    $cardSecDeposit = DB::table('card_type')
                        ->where('card_type_id', '=', $pass->card_type_id)
                        ->first('card_sec') ?: throw new PDOException("Failed To Found Card Security");


                    DB::table('cl_status')
                        ->updateOrInsert(
                            ['engraved_id'      => $transaction['engraved_id']],
                            [
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $cardSecDeposit->card_sec,
                            'tp_balance'        => $transaction['rem_trips'],
                            'pass_expiry'       => $transaction['pass_expiry'],
                            'src_stn_id'        => $transaction['src_stn_id'],
                            'des_stn_id'        => $transaction['des_stn_id'],
                            'auto_topup_status' => $autoTopUpStatus,
                            'auto_topup_amt'    => $autoTopUpAmount,
                            'bonus_points'      => $bonusPoints,
                            'is_test'           => $transaction['is_test'],
                            'pax_first_name'    => $paxFirstName,
                            'pax_last_name'     => $paxLastName,
                            'pax_mobile'        => $paxMobile,
                            'pax_gen_type'      => $paxGenType,
                            'updated_at'        => now()
                        ]);

                    $oldData = DB::table('cl_sn_mapping')
                        ->where('engraved_id','=',$transaction['old_engraved_id'])
                        ->first();

                    DB::table('cl_blacklist')->insert([
                        'ms_blk_reason_id'  => 1,
                        'start_date'        => Carbon::now(),
                        'engraved_id'       => $oldData->engraved_id,
                        'chip_id'           => $oldData->chip_id,
                    ]);

                }

            }

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        } catch (PDOException $e) {

            Log::channel('ClAccounting')->error($e);
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
    }/* OP_TYPE_ID = 13*/

    public function overTravel($transaction)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName = "";
        $paxLastName = "";
        $paxMobile = 123456789;
        $paxGenType = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName   = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile        = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType     = $transaction['pax_gen_type'];

        if ($transaction['product_id'] == 4) {

            DB::beginTransaction();

            try {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => $transaction['op_type_id'],
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_tp_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 14 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 14,
                        'pen_price'     => $transaction['total_charges'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

                /* PEN TYPE ID 24 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 24,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {
                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;
            }

            DB::commit();
            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        }

        $transData['error'] = "Product Not Found !";
        $transData['atek_id'] = $transaction['atek_id'];
        return $transData;


    }/* OP_TYPE_ID = 14 & 24*/

    public function excessTimeSameStation($transaction)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName = "";
        $paxLastName = "";
        $paxMobile = 123456789;
        $paxGenType = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName   = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile        = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType     = $transaction['pax_gen_type'];

        if ($transaction['product_id'] == 3) {

            //DB::beginTransaction();

            try {

                DB::table('cl_sv_accounting')->insert([
                    'atek_id'          => $transaction['atek_id'],
                    'txn_date'         => $transaction['txn_date'],
                    'engraved_id'      => $transaction['engraved_id'],
                    'op_type_id'       => 61,
                    'stn_id'           => $transaction['stn_id'],
                    'cash_col'         => $transaction['cash_col'],
                    'cash_ret'         => $transaction['cash_ret'],
                    'pass_price'       => $transaction['pass_price'],
                    'card_fee'         => $transaction['card_fee'],
                    'card_sec'         => $transaction['card_sec'],
                    'processing_fee'   => $transaction['processing_fee'],
                    'total_price'      => $transaction['total_price'],
                    'pass_ref_chr'     => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr' => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr' => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'     => $transaction['pre_chip_bal'],
                    'pos_chip_bal'     => $transaction['pos_chip_bal'],
                    'media_type_id'    => $transaction['media_type_id'],
                    'product_id'       => $transaction['product_id'],
                    'pass_id'          => $transaction['pass_id'],
                    'pass_expiry'      => $transaction['pass_expiry'],
                    'pax_first_name'   => $paxFirstName,
                    'pax_last_name'    => $paxLastName,
                    'pax_mobile'       => $paxMobile,
                    'pax_gen_type'     => $paxGenType,
                    'shift_id'         => $transaction['shift_id'],
                    'user_id'          => $transaction['user_id'],
                    'eq_id'            => $transaction['eq_id'],
                    'pay_type_id'      => $transaction['pay_type_id'],
                    'pay_ref'          => $transaction['pay_ref'],
                    'is_test'          => $transaction['is_test'],
                    'old_engraved_id'  => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_sv_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 14 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 31,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {

                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                //DB::rollBack();

                return $transData;

            }

            //DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id']    = $transaction['atek_id'];
            return $transData;

        }

        if ($transaction['product_id'] == 4) {

            DB::beginTransaction();

            try {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 61,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_tp_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 31 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 31,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {
                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error']   = $e->getMessage();

                DB::rollBack();

                return $transData;
            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id']    = $transaction['atek_id'];
            return $transData;

        }

        $transData['error'] = "Product Not Found !";
        $transData['atek_id'] = $transaction['atek_id'];
        return $transData;

    }/* OP_TYPE_ID = 61*/

    public function excessTimeExitStation($transaction)
    {
        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName = "";
        $paxLastName  = "";
        $paxMobile    = 123456789;
        $paxGenType   = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName   = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile        = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType     = $transaction['pax_gen_type'];

        if ($transaction['product_id'] == 3) {

            DB::beginTransaction();

            try {

                DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 62,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_sv_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 14 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 32,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {

                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;

            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id']    = $transaction['atek_id'];
            return $transData;

        }

        if ($transaction['product_id'] == 4) {

            //DB::beginTransaction();

            try {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 62,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_tp_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 31 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 32,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {
                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;
            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        }

        $transData['error'] = "Product Not Found !";
        $transData['atek_id'] = $transaction['atek_id'];
        return $transData;


    }/* OP_TYPE_ID = 62*/

    public function entryMismatchSameTime($transaction)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName = "";
        $paxLastName  = "";
        $paxMobile    = 123456789;
        $paxGenType   = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName   = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile        = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType     = $transaction['pax_gen_type'];

        if ($transaction['product_id'] == 3) {

            DB::beginTransaction();

            try {

                DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 63,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_sv_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 14 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 33,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {

                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error']   = $e->getMessage();

                DB::rollBack();

                return $transData;

            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id']    = $transaction['atek_id'];
            return $transData;

        }

        if ($transaction['product_id'] == 4) {

            //DB::beginTransaction();

            try {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 63,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_tp_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 31 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 33,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {
                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;
            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        }

        $transData['error'] = "Product Not Found !";
        $transData['atek_id'] = $transaction['atek_id'];
        return $transData;
    }/* OP_TYPE_ID = 63*/

    public function entryMismatchOtherTime($transaction){

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName = "";
        $paxLastName = "";
        $paxMobile = 123456789;
        $paxGenType = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType = $transaction['pax_gen_type'];

        if ($transaction['product_id'] == 3) {

            DB::beginTransaction();

            try {

                DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 64,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_sv_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 14 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 34,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {

                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;

            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        }

        if ($transaction['product_id'] == 4) {

            //DB::beginTransaction();

            try {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 64,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_tp_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 31 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 34,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {
                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;
            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        }

        $transData['error'] = "Product Not Found !";
        $transData['atek_id'] = $transaction['atek_id'];
        return $transData;

    }/* OP_TYPE_ID = 64*/

    public function exitMismatchNoExit($transaction)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName   = "";
        $paxLastName    = "";
        $paxMobile      = 123456789;
        $paxGenType     = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName   = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile        = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType     = $transaction['pax_gen_type'];

        if ($transaction['product_id'] == 3) {

            DB::beginTransaction();

            try {

                DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 65,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_sv_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 14 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 35,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {

                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;

            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        }

        if ($transaction['product_id'] == 4) {

            //DB::beginTransaction();

            try {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 65,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_tp_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 31 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 35,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {
                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;
            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        }

        $transData['error'] = "Product Not Found !";
        $transData['atek_id'] = $transaction['atek_id'];
        return $transData;

    }/* OP_TYPE_ID = 65*/

    public function exitMismatchNoEntry($transaction)
    {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName = "";
        $paxLastName  = "";
        $paxMobile    = 123456789;
        $paxGenType   = 0;

        if (array_key_exists("pax_first_name", $transaction)) $paxFirstName = $transaction['pax_first_name'];
        if (array_key_exists("pax_last_name", $transaction)) $paxLastName   = $transaction['pax_last_name'];
        if (array_key_exists("pax_mobile", $transaction)) $paxMobile        = $transaction['pax_mobile'];
        if (array_key_exists("pax_gen_type", $transaction)) $paxGenType     = $transaction['pax_gen_type'];

        if ($transaction['product_id'] == 3) {

            DB::beginTransaction();

            try {

                DB::table('cl_sv_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 66,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'pre_chip_bal'      => $transaction['pre_chip_bal'],
                    'pos_chip_bal'      => $transaction['pos_chip_bal'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_sv_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 14 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 36,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {

                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;

            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id']    = $transaction['atek_id'];
            return $transData;

        }

        if ($transaction['product_id'] == 4) {

            //DB::beginTransaction();

            try {

                DB::table('cl_tp_accounting')->insert([
                    'atek_id'           => $transaction['atek_id'],
                    'txn_date'          => $transaction['txn_date'],
                    'engraved_id'       => $transaction['engraved_id'],
                    'op_type_id'        => 66,
                    'stn_id'            => $transaction['stn_id'],
                    'cash_col'          => $transaction['cash_col'],
                    'cash_ret'          => $transaction['cash_ret'],
                    'pass_price'        => $transaction['pass_price'],
                    'card_fee'          => $transaction['card_fee'],
                    'card_sec'          => $transaction['card_sec'],
                    'processing_fee'    => $transaction['processing_fee'],
                    'total_price'       => $transaction['total_price'],
                    'pass_ref_chr'      => $transaction['pass_ref_chr'],
                    'card_fee_ref_chr'  => $transaction['card_fee_ref_chr'],
                    'card_sec_ref_chr'  => $transaction['card_sec_ref_chr'],
                    'num_trips'         => $transaction['num_trips'],
                    'rem_trips'         => $transaction['rem_trips'],
                    'media_type_id'     => $transaction['media_type_id'],
                    'product_id'        => $transaction['product_id'],
                    'pass_id'           => $transaction['pass_id'],
                    'pass_expiry'       => $transaction['pass_expiry'],
                    'src_stn_id'        => $transaction['src_stn_id'],
                    'des_stn_id'        => $transaction['des_stn_id'],
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_mobile'        => $paxMobile,
                    'pax_gen_type'      => $paxGenType,
                    'shift_id'          => $transaction['shift_id'],
                    'user_id'           => $transaction['user_id'],
                    'eq_id'             => $transaction['eq_id'],
                    'pay_type_id'       => $transaction['pay_type_id'],
                    'pay_ref'           => $transaction['pay_ref'],
                    'is_test'           => $transaction['is_test'],
                    'old_engraved_id'   => $transaction['old_engraved_id'],
                ]);

                $clAccId = DB::table('cl_tp_accounting')
                    ->where('atek_id', '=', $transaction['atek_id'])
                    ->select('cl_acc_id')
                    ->value('cl_acc_id');

                /* PEN TYPE ID 31 */
                DB::table('cl_pen_accounting')
                    ->insert([
                        'cl_acc_id'     => $clAccId,
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'pen_type_id'   => 36,
                        'pen_price'     => $transaction['total_penalty'],
                        'stn_id'        => $transaction['stn_id'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                    ]);

            } catch (PDOException $e) {
                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }
                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error'] = $e->getMessage();

                DB::rollBack();

                return $transData;
            }

            DB::commit();

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atek_id'];
            return $transData;

        }

        $transData['error'] = "Product Not Found !";
        $transData['atek_id'] = $transaction['atek_id'];
        return $transData;

    }/* OP_TYPE_ID = 66*/

}
