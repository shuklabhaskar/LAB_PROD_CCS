<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class ClAccounting extends Controller
{
    public function ClAccounting(Request $request)
    {

        $validator = Validator::make($request->all(), [
            '*.atek_id'        => 'required',
            '*.des_stn_id'     => 'required|integer',
            '*.engraved_id'    => 'required',
            '*.eq_id'          => 'required|string',
            '*.media_type_id'  => 'required|integer',
            '*.pass_id'        => 'required',
            '*.src_stn_id'     => 'required|integer',
            '*.stn_id'         => 'required|integer',
            '*.txn_date'       => 'required'
        ]);

        if ($validator->fails()) {
            return response([
               'status' => false,
               'error' => json_encode($validator->errors())
            ]);
        }

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

            $engravedIdExists = DB::table('cl_status')
                ->where('engraved_id','=',$transaction['engraved_id'])
                ->value('engraved_id');

            if ($opTypeId == 1) { //ISSUANCE

                $transData = $this->Issuance($transaction , $engravedIdExists);
                $response[] = $transData;

            } elseif ($opTypeId == 3) { // RELOAD

                $transData = $this->Reload($transaction  , $engravedIdExists);
                $response[] = $transData;

            } elseif ($opTypeId == 6) { // REFUND

                $transData = $this->Refund($transaction  , $engravedIdExists);
                $response[] = $transData;

            } elseif ($opTypeId == 11) {

                /**
                    REPLACEMENT IN CASE OF CARD NOT OKAY & PHYSICALLY OKAY
                **/

                $transData = $this->cardNotReadablePhysicallyOkay($transaction);
                $response[] = $transData;

            }elseif ($opTypeId == 12) { // REFUND

                /**
                REPLACEMENT IN CASE OF CARD NOT OKAY & PHYSICALLY NOT OKAY
                 **/

                $transData = $this->cardNotReadablePhysicallyNotOkay($transaction);
                $response[] = $transData;

            }elseif ($opTypeId == 13) { // REFUND

                /**
                LOST CARD
                 **/

                $transData = $this->lostCard($transaction);
                $response[] = $transData;

            }else {

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

    public function Issuance($transaction , $engravedIdExists) {

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName       = "";
        $paxLastName        = "";
        $paxMobile          = 123456789;
        $paxGenType         = 0;
        $autoTopUpStatus    = false;
        $autoTopUpAmount    = 0.0;
        $bonusPoints        = 0.0;

        if(array_key_exists("pax_first_name", $transaction))  $paxFirstName = $transaction['pax_first_name'];
        if(array_key_exists("pax_last_name", $transaction))  $paxLastName = $transaction['pax_last_name'];
        if(array_key_exists("pax_mobile", $transaction))  $paxMobile = $transaction['pax_mobile'];
        if(array_key_exists("pax_gen_type", $transaction))  $paxGenType = $transaction['pax_gen_type'];
        if(array_key_exists("auto_topup_status", $transaction))  $autoTopUpStatus = $transaction['auto_topup_status'];
        if(array_key_exists("auto_topup_amt", $transaction))  $autoTopUpAmount = $transaction['auto_topup_amt'];
        if(array_key_exists("bonus_points", $transaction))  $bonusPoints = $transaction['bonus_points'];

        try {

            if ($transaction['product_id'] == 3){

                 $svData =  DB::table('cl_sv_accounting')->insert([
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

                 if ($svData){

                    if ($engravedIdExists) {
                        /* FOR CL SV ISSUANCE */

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
                                    'sv_balance'        => $transaction['pos_chip_bal'],
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
                                    'updated_at'        => now(),
                                ]);

                    }

                     if ($engravedIdExists == null){

                             DB::table('cl_status')->insert([
                                 'engraved_id'       =>  $transaction['engraved_id'],
                                 'chip_id'           =>  $transaction['chip_id'],
                                 'txn_date'          =>  $transaction['txn_date'],
                                 'pass_id'           =>  $transaction['pass_id'],
                                 'product_id'        =>  $transaction['product_id'],
                                 'card_fee'          =>  $transaction['card_fee'],
                                 'card_sec'          =>  $transaction['card_sec'],
                                 'sv_balance'        =>  $transaction['pos_chip_bal'],
                                 'pass_expiry'       =>  $transaction['pass_expiry'],
                                 'src_stn_id'        =>  $transaction['src_stn_id'],
                                 'des_stn_id'        =>  $transaction['des_stn_id'],
                                 'auto_topup_status' =>  $autoTopUpStatus,
                                 'auto_topup_amt'    =>  $autoTopUpAmount,
                                 'bonus_points'      =>  $bonusPoints,
                                 'is_test'           =>  $transaction['is_test'],
                                 'pax_first_name'    =>  $paxFirstName,
                                 'pax_last_name'     =>  $paxLastName,
                                 'pax_mobile'        =>  $paxMobile,
                                 'pax_gen_type'      =>  $paxGenType,
                             ]);

                     }

                }

            }

            if ($transaction['product_id'] == 4) {


                $TpData =  DB::table('cl_tp_accounting')->insert([
                    'atek_id'          => $transaction['atek_id'],
                    'txn_date'         => $transaction['txn_date'],
                    'engraved_id'      => $transaction['engraved_id'],
                    'op_type_id'       => $transaction['op_type_id'],
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
                    'num_trips'        => $transaction['num_trips'],
                    'rem_trips'        => $transaction['rem_trips'],
                    'media_type_id'    => $transaction['media_type_id'],
                    'product_id'       => $transaction['product_id'],
                    'pass_id'          => $transaction['pass_id'],
                    'pass_expiry'      => $transaction['pass_expiry'],
                    'src_stn_id'       => $transaction['src_stn_id'],
                    'des_stn_id'       => $transaction['des_stn_id'],
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

                if ($TpData) {

                    if ($engravedIdExists){

                        DB::table('cl_status')
                            ->where('engraved_id','=',$transaction['engraved_id'])
                            ->update([
                            'engraved_id'       =>  $transaction['engraved_id'],
                            'chip_id'           =>  $transaction['chip_id'],
                            'txn_date'          =>  $transaction['txn_date'],
                            'pass_id'           =>  $transaction['pass_id'],
                            'product_id'        =>  $transaction['product_id'],
                            'card_fee'          =>  $transaction['card_fee'],
                            'card_sec'          =>  $transaction['card_sec'],
                            'tp_balance'        =>  $transaction['rem_trips'],
                            'pass_expiry'       =>  $transaction['pass_expiry'],
                            'src_stn_id'        =>  $transaction['src_stn_id'],
                            'des_stn_id'        =>  $transaction['des_stn_id'],
                            'auto_topup_status' =>  $autoTopUpStatus,
                            'auto_topup_amt'    =>  $autoTopUpAmount,
                            'bonus_points'      =>  $bonusPoints,
                            'is_test'           =>  $transaction['is_test'],
                            'pax_first_name'    =>  $paxFirstName,
                            'pax_last_name'     =>  $paxLastName,
                            'pax_mobile'        =>  $paxMobile,
                            'pax_gen_type'      =>  $paxGenType,
                             'updated_at'       =>  now()

                        ]);
                    }

                    if ($engravedIdExists == null){

                            DB::table('cl_status')->insert([
                                'engraved_id'       =>$transaction['engraved_id'],
                                'chip_id'           =>$transaction['chip_id'],
                                'txn_date'          =>$transaction['txn_date'],
                                'pass_id'           =>$transaction['pass_id'],
                                'product_id'        =>$transaction['product_id'],
                                'card_fee'          =>$transaction['card_fee'],
                                'card_sec'          =>$transaction['card_sec'],
                                'tp_balance'        =>$transaction['rem_trips'],
                                'pass_expiry'       =>$transaction['pass_expiry'],
                                'src_stn_id'        =>$transaction['src_stn_id'],
                                'des_stn_id'        =>$transaction['des_stn_id'],
                                'auto_topup_status' =>$autoTopUpStatus,
                                'auto_topup_amt'    =>$autoTopUpAmount,
                                'bonus_points'      =>$bonusPoints,
                                'is_test'           =>$transaction['is_test'],
                                'pax_first_name'    =>$paxFirstName,
                                'pax_last_name'     =>$paxLastName,
                                'pax_mobile'        =>$paxMobile,
                                'pax_gen_type'      =>$paxGenType,

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


    } /* OP_TYPE_ID = 1*/

    public function Reload($transaction , $engravedIdExists){

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName       = "";
        $paxLastName        = "";
        $paxMobile          = 123456789;
        $paxGenType         = 0;
        $autoTopUpStatus    = false;
        $autoTopUpAmount    = 0.0;
        $bonusPoints        = 0.0;


        if(array_key_exists("pax_first_name", $transaction))  $paxFirstName = $transaction['pax_first_name'];
        if(array_key_exists("pax_last_name", $transaction))  $paxLastName = $transaction['pax_last_name'];
        if(array_key_exists("pax_mobile", $transaction))  $paxMobile = $transaction['pax_mobile'];
        if(array_key_exists("pax_gen_type", $transaction))  $paxGenType = $transaction['pax_gen_type'];
        if(array_key_exists("auto_topup_status", $transaction))  $autoTopUpStatus = $transaction['auto_topup_status'];
        if(array_key_exists("auto_topup_amt", $transaction))  $autoTopUpAmount = $transaction['auto_topup_amt'];
        if(array_key_exists("bonus_points", $transaction))  $bonusPoints = $transaction['bonus_points'];


        try {

            if ($transaction['product_id'] == 3 ){

                $svData =  DB::table('cl_sv_accounting')->insert([
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

                if ($svData){

                    if ($engravedIdExists){

                        /* FOR CL SV ISSUANCE */


                            DB::table('cl_status')
                                ->where('engraved_id','=',$transaction['engraved_id'])
                                ->update([
                                    'chip_id'           => $transaction['chip_id'],
                                    'txn_date'          => $transaction['txn_date'],
                                    'pass_id'           => $transaction['pass_id'],
                                    'product_id'        => $transaction['product_id'],
                                    'card_fee'          => $transaction['card_fee'],
                                    'card_sec'          => $transaction['card_sec'],
                                    'sv_balance'        => $transaction['pos_chip_bal'],
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
                                    'updated_at'        => now(),
                                ]);


                    }

                }
            }

            if ($transaction['product_id'] == 4 ) {

                $tpData =  DB::table('cl_tp_accounting')->insert([
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

                if ($tpData){

                    if ($engravedIdExists){

                        /* FOR CL TP ISSUANCE */
                        DB::table('cl_status')
                            ->where('engraved_id','=',$transaction['engraved_id'])
                            ->update([
                                'engraved_id'       =>  $transaction['engraved_id'],
                                'chip_id'           =>  $transaction['chip_id'],
                                'txn_date'          =>  $transaction['txn_date'],
                                'pass_id'           =>  $transaction['pass_id'],
                                'product_id'        =>  $transaction['product_id'],
                                'card_fee'          =>  $transaction['card_fee'],
                                'card_sec'          =>  $transaction['card_sec'],
                                'tp_balance'        =>  $transaction['rem_trips'],
                                'pass_expiry'       =>  $transaction['pass_expiry'],
                                'src_stn_id'        =>  $transaction['src_stn_id'],
                                'des_stn_id'        =>  $transaction['des_stn_id'],
                                'auto_topup_status' =>  $autoTopUpStatus,
                                'auto_topup_amt'    =>  $autoTopUpAmount,
                                'bonus_points'      =>  $bonusPoints,
                                'is_test'           =>  $transaction['is_test'],
                                'pax_first_name'    =>  $paxFirstName,
                                'pax_last_name'     =>  $paxLastName,
                                'pax_mobile'        =>  $paxMobile,
                                'pax_gen_type'      =>  $paxGenType,
                                'updated_at'        =>  now()
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


    } /* OP_TYPE_ID = 3*/

    public function Refund($transaction , $engravedIdExists){

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName       = "";
        $paxLastName        = "";
        $paxMobile          = 123456789;
        $paxGenType         = 0;

        if(array_key_exists("pax_first_name", $transaction))  $paxFirstName = $transaction['pax_first_name'];
        if(array_key_exists("pax_last_name", $transaction))  $paxLastName = $transaction['pax_last_name'];
        if(array_key_exists("pax_mobile", $transaction))  $paxMobile = $transaction['pax_mobile'];
        if(array_key_exists("pax_gen_type", $transaction))  $paxGenType = $transaction['pax_gen_type'];

        try {

            if ($transaction['product_id'] == 3) {

                $svData =  DB::table('cl_sv_accounting')->insert([
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

                if ($svData){
                    if ($engravedIdExists){

                        DB::table('cl_status')
                            ->where('engraved_id','=',$transaction['engraved_id'])
                            ->update([
                                'engraved_id'       =>  $transaction['engraved_id'],
                                'chip_id'           =>  $transaction['chip_id'],
                                'txn_date'          =>  Carbon::now(),
                                'pass_id'           =>  0,
                                'product_id'        =>  0,
                                'card_fee'          =>  0,
                                'card_sec'          =>  0,
                                'sv_balance'        =>  0,
                                'pass_expiry'       =>  Carbon::now(),
                                'src_stn_id'        =>  0,
                                'des_stn_id'        =>  0,
                                'auto_topup_status' =>  false,
                                'auto_topup_amt'    =>  0,
                                'bonus_points'      =>  0,
                                'is_test'           =>  false,
                                'pax_first_name'    =>  "",
                                'pax_last_name'     =>  "",
                                'pax_mobile'        =>  0000000000,
                                'pax_gen_type'      =>  0,
                            ]);

                    }
                }

            }

            if ($transaction['product_id'] == 4) {

                $tpData =  DB::table('cl_tp_accounting')->insert([
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

                if ($tpData){
                    if ($engravedIdExists){

                        DB::table('cl_status')
                            ->where('engraved_id','=',$transaction['engraved_id'])
                            ->update([
                                'engraved_id'       =>  $transaction['engraved_id'],
                                'chip_id'           =>  $transaction['chip_id'],
                                'txn_date'          =>  Carbon::now(),
                                'pass_id'           =>  0,
                                'product_id'        =>  0,
                                'card_fee'          =>  0,
                                'card_sec'          =>  0,
                                'sv_balance'        =>  0,
                                'pass_expiry'       =>  Carbon::now(),
                                'src_stn_id'        =>  0,
                                'des_stn_id'        =>  0,
                                'auto_topup_status' =>  false,
                                'auto_topup_amt'    =>  0,
                                'bonus_points'      =>  0,
                                'is_test'           =>  false,
                                'pax_first_name'    =>  "",
                                'pax_last_name'     =>  "",
                                'pax_mobile'        =>  0000000000,
                                'pax_gen_type'      =>  0,
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

    public function cardNotReadablePhysicallyOkay($transaction){

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName       = "";
        $paxLastName        = "";
        $paxMobile          = 123456789;
        $paxGenType         = 0;
        $autoTopUpStatus    = false;
        $autoTopUpAmount    = 0.0;
        $bonusPoints        = 0.0;

        if(array_key_exists("pax_first_name", $transaction))  $paxFirstName = $transaction['pax_first_name'];
        if(array_key_exists("pax_last_name", $transaction))  $paxLastName = $transaction['pax_last_name'];
        if(array_key_exists("pax_mobile", $transaction))  $paxMobile = $transaction['pax_mobile'];
        if(array_key_exists("pax_gen_type", $transaction))  $paxGenType = $transaction['pax_gen_type'];
        if(array_key_exists("auto_topup_status", $transaction))  $autoTopUpStatus = $transaction['auto_topup_status'];
        if(array_key_exists("auto_topup_amt", $transaction))  $autoTopUpAmount = $transaction['auto_topup_amt'];
        if(array_key_exists("bonus_points", $transaction))  $bonusPoints = $transaction['bonus_points'];


        try {

            if ($transaction['product_id'] == 3) {

                $svData =  DB::table('cl_sv_accounting')->insert([
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

                if ($svData){

                    DB::table('cl_status')
                        ->insert([
                            'engraved_id'       => $transaction['engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $transaction['card_sec'],
                            'sv_balance'        => $transaction['pos_chip_bal'],
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
                            'updated_at'        => now(),
                        ]);

                        DB::table('cl_blacklist')->insert([
                            'ms_blk_reason_id'  => 3,
                            'start_date'        => Carbon::now(),
                            'engraved_id'       => $transaction['old_engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                        ]);
                }

            }

            if ($transaction['product_id'] == 4) {

                $tpData =  DB::table('cl_tp_accounting')->insert([
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


                if ($tpData){

                    DB::table('cl_status')
                        ->insert([
                            'engraved_id'       =>  $transaction['engraved_id'],
                            'chip_id'           =>  $transaction['chip_id'],
                            'txn_date'          =>  $transaction['txn_date'],
                            'pass_id'           =>  $transaction['pass_id'],
                            'product_id'        =>  $transaction['product_id'],
                            'card_fee'          =>  $transaction['card_fee'],
                            'card_sec'          =>  $transaction['card_sec'],
                            'tp_balance'        =>  $transaction['rem_trips'],
                            'pass_expiry'       =>  $transaction['pass_expiry'],
                            'src_stn_id'        =>  $transaction['src_stn_id'],
                            'des_stn_id'        =>  $transaction['des_stn_id'],
                            'auto_topup_status' =>  $autoTopUpStatus,
                            'auto_topup_amt'    =>  $autoTopUpAmount,
                            'bonus_points'      =>  $bonusPoints,
                            'is_test'           =>  $transaction['is_test'],
                            'pax_first_name'    =>  $paxFirstName,
                            'pax_last_name'     =>  $paxLastName,
                            'pax_mobile'        =>  $paxMobile,
                            'pax_gen_type'      =>  $paxGenType,
                            'updated_at'        =>  now()

                        ]);

                        DB::table('cl_blacklist')->insert([
                            'ms_blk_reason_id'  => 3,
                            'start_date'        => Carbon::now(),
                            'engraved_id'       => $transaction['old_engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                        ]);


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
    } /* OP_TYPE_ID = 11*/

    public function cardNotReadablePhysicallyNotOkay($transaction){

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName       = "";
        $paxLastName        = "";
        $paxMobile          = 123456789;
        $autoTopUpStatus    = false;
        $paxGenType       = 0;
        $autoTopUpAmount    = 0.0;
        $bonusPoints        = 0.0;

        if(array_key_exists("pax_first_name", $transaction))  $paxFirstName = $transaction['pax_first_name'];
        if(array_key_exists("pax_last_name", $transaction))  $paxLastName = $transaction['pax_last_name'];
        if(array_key_exists("pax_mobile", $transaction))  $paxMobile = $transaction['pax_mobile'];
        if(array_key_exists("pax_gen_type", $transaction))  $paxGenType = $transaction['pax_gen_type'];
        if(array_key_exists("auto_topup_status", $transaction))  $autoTopUpStatus = $transaction['auto_topup_status'];
        if(array_key_exists("auto_topup_amt", $transaction))  $autoTopUpAmount = $transaction['auto_topup_amt'];
        if(array_key_exists("bonus_points", $transaction))  $bonusPoints = $transaction['bonus_points'];

        try {

            if ($transaction['product_id'] == 3) {

                $svData =  DB::table('cl_sv_accounting')->insert([
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

                if ($svData){

                    DB::table('cl_status')
                        ->insert([
                            'engraved_id'       => $transaction['engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $transaction['card_sec'],
                            'sv_balance'        => $transaction['pos_chip_bal'],
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
                            'updated_at'        => now(),
                        ]);

                        DB::table('cl_blacklist')->insert([
                            'ms_blk_reason_id'  => 2,
                            'start_date'        => Carbon::now(),
                            'engraved_id'       => $transaction['old_engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                        ]);


                }

            }

            if ($transaction['product_id'] == 4) {

                $tpData =  DB::table('cl_tp_accounting')->insert([
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

                if ($tpData){

                    DB::table('cl_status')
                        ->insert([
                            'engraved_id'       =>  $transaction['engraved_id'],
                            'chip_id'           =>  $transaction['chip_id'],
                            'txn_date'          =>  $transaction['txn_date'],
                            'pass_id'           =>  $transaction['pass_id'],
                            'product_id'        =>  $transaction['product_id'],
                            'card_fee'          =>  $transaction['card_fee'],
                            'card_sec'          =>  $transaction['card_sec'],
                            'tp_balance'        =>  $transaction['rem_trips'],
                            'pass_expiry'       =>  $transaction['pass_expiry'],
                            'src_stn_id'        =>  $transaction['src_stn_id'],
                            'des_stn_id'        =>  $transaction['des_stn_id'],
                            'auto_topup_status' =>  $autoTopUpStatus,
                            'auto_topup_amt'    =>  $autoTopUpAmount,
                            'bonus_points'      =>  $bonusPoints,
                            'is_test'           =>  $transaction['is_test'],
                            'pax_first_name'    =>  $paxFirstName,
                            'pax_last_name'     =>  $paxLastName,
                            'pax_mobile'        =>  $paxMobile,
                            'pax_gen_type'      =>  $paxGenType,
                            'updated_at'        =>  now()

                        ]);

                        DB::table('cl_blacklist')->insert([
                            'ms_blk_reason_id'  => 2,
                            'start_date'        => Carbon::now(),
                            'engraved_id'       => $transaction['old_engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                        ]);


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
    } /* OP_TYPE_ID = 12*/

    public function lostCard($transaction){

        /* CHECK THAT IS THESE ATTRIBUTES ARE NULLABLE OR NOT */
        $paxFirstName       = "";
        $paxLastName        = "";
        $paxMobile          = 123456789;
        $paxGenType       = 0;
        $autoTopUpStatus    = false;
        $autoTopUpAmount    = 0.0;
        $bonusPoints        = 0.0;

        if(array_key_exists("pax_first_name", $transaction))  $paxFirstName = $transaction['pax_first_name'];
        if(array_key_exists("pax_last_name", $transaction))  $paxLastName = $transaction['pax_last_name'];
        if(array_key_exists("pax_mobile", $transaction))  $paxMobile = $transaction['pax_mobile'];
        if(array_key_exists("pax_gen_type", $transaction))  $paxGenType = $transaction['pax_gen_type'];
        if(array_key_exists("auto_topup_status", $transaction))  $autoTopUpStatus = $transaction['auto_topup_status'];
        if(array_key_exists("auto_topup_amt", $transaction))  $autoTopUpAmount = $transaction['auto_topup_amt'];
        if(array_key_exists("bonus_points", $transaction))  $bonusPoints = $transaction['bonus_points'];

        try {

            if ($transaction['product_id'] == 3) {

                $svData =  DB::table('cl_sv_accounting')->insert([
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

                if ($svData){

                    DB::table('cl_status')
                        ->insert([
                            'engraved_id'       => $transaction['engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => $transaction['txn_date'],
                            'pass_id'           => $transaction['pass_id'],
                            'product_id'        => $transaction['product_id'],
                            'card_fee'          => $transaction['card_fee'],
                            'card_sec'          => $transaction['card_sec'],
                            'sv_balance'        => $transaction['pos_chip_bal'],
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
                            'updated_at'        => now(),
                        ]);

                        DB::table('cl_blacklist')->insert([
                            'ms_blk_reason_id'  => 1,
                            'start_date'        => Carbon::now(),
                            'engraved_id'       => $transaction['old_engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                        ]);

                }

            }

            if ($transaction['product_id'] == 4) {

                $tpData =  DB::table('cl_tp_accounting')->insert([
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

                if ($tpData){

                    DB::table('cl_status')
                        ->insert([
                            'engraved_id'       =>  $transaction['engraved_id'],
                            'chip_id'           =>  $transaction['chip_id'],
                            'txn_date'          =>  $transaction['txn_date'],
                            'pass_id'           =>  $transaction['pass_id'],
                            'product_id'        =>  $transaction['product_id'],
                            'card_fee'          =>  $transaction['card_fee'],
                            'card_sec'          =>  $transaction['card_sec'],
                            'tp_balance'        =>  $transaction['rem_trips'],
                            'pass_expiry'       =>  $transaction['pass_expiry'],
                            'src_stn_id'        =>  $transaction['src_stn_id'],
                            'des_stn_id'        =>  $transaction['des_stn_id'],
                            'auto_topup_status' =>  $autoTopUpStatus,
                            'auto_topup_amt'    =>  $autoTopUpAmount,
                            'bonus_points'      =>  $bonusPoints,
                            'is_test'           =>  $transaction['is_test'],
                            'pax_first_name'    =>  $paxFirstName,
                            'pax_last_name'     =>  $paxLastName,
                            'pax_mobile'        =>  $paxMobile,
                            'pax_gen_type'      =>  $paxGenType,
                            'updated_at'        =>  now()

                        ]);

                        DB::table('cl_blacklist')->insert([
                            'ms_blk_reason_id'  => 1,
                            'start_date'        => Carbon::now(),
                            'engraved_id'       => $transaction['old_engraved_id'],
                            'chip_id'           => $transaction['chip_id'],
                        ]);

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
    }/* OP_TYPE_ID = 13*/

}
