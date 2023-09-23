<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClCardReplacement extends Controller
{
    public function store(Request $request): Response|Application|ResponseFactory
    {

        $transactions = json_decode($request->getContent(), true);
        $response = [];

        if ($transactions == []){

            return response([
                'status' => false,
                'error' => "please provide required data!"
            ]);

        }

        foreach ($transactions as $transaction) {

            /* CHECK THAT IS TEST IS NULLABLE OR NOT */

            $paxFirstName       = "";
            $paxLastName        = "";
            $paxGender          = 0;
            $paxMobile          = 1234567891;
            $autoTopUpStatus    = false;
            $autoTopUpAmount    = 0.0;
            $bonusPoints        = 0.0;

            if(array_key_exists("paxFirstName", $transaction))  $paxFirstName = $transaction['paxFirstName'];
            if(array_key_exists("paxLastName", $transaction))  $paxLastName = $transaction['paxLastName'];
            if(array_key_exists("pax_gen_type", $transaction))  $paxGender = $transaction['pax_gen_type'];
            if(array_key_exists("paxMobile", $transaction))  $paxMobile = $transaction['paxMobile'];
            if(array_key_exists("auto_topup_status", $transaction))  $autoTopUpStatus = $transaction['auto_topup_status'];
            if(array_key_exists("auto_topup_amt", $transaction))  $autoTopUpAmount = $transaction['auto_topup_amt'];
            if(array_key_exists("bonus_points", $transaction))  $bonusPoints = $transaction['bonus_points'];

            if ($transaction['product_id'] == 3){

                $checkTrue = DB::table('cl_card_rep')->insert([
                    'atek_id'          => $transaction['atek_id'],
                    'txn_date'         => $transaction['txn_date'],
                    'engraved_id'      => $transaction['engraved_id'],
                    'chip_id'          => $transaction['chip_id'],
                    'stn_id'           => $transaction['stn_id'],
                    'sv_balance'       => $transaction['sv_balance'],
                    'card_sec'         => $transaction['card_sec'],
                    'card_fee'         => 0,
                    'pass_id'          => $transaction['pass_id'],
                    'product_id'       => $transaction['product_id'],
                    'pass_expiry'      => $transaction['pass_expiry'],
                    'src_stn_id'       => $transaction['src_stn_id'],
                    'des_stn_id'       => $transaction['des_stn_id'],
                    'tid'              => $transaction['tid'],
                    'eq_id'            => $transaction['eq_id'],
                    'eq_type_id'       => $transaction['eq_type_id'],
                    'pax_first_name'   => $paxFirstName,
                    'pax_last_name'    => $paxLastName,
                    'pax_mobile'       => $paxMobile,
                ]);

                if ($checkTrue) {

                    DB::table('cl_status')->insert([
                        'engraved_id'       => $transaction['engravedId'],
                        'chip_id'           => $transaction['chipId'],
                        'txn_date'          => $transaction['txnDate'],
                        'pass_id'           => $transaction['passId'],
                        'product_id'        => $transaction['productID'], //
                        'card_fee'          => $transaction['card_fee'],
                        'sv_balance'        => $transaction['sv_balance'],
                        'pass_expiry_date'  => $transaction['passExpiry'],
                        'src_stn_id'        => $transaction['srcStnId'],
                        'des_stn_id'        => $transaction['desStnId'],
                        'auto_topup_status' => $autoTopUpStatus,
                        'auto_topup_amt'    => $autoTopUpAmount,
                        'bonus_points'      => $bonusPoints,
                        'is_test'           => false,
                        'pax_name'          => $paxFirstName,
                        'pax_gen_type'      => $paxGender,//
                        'pax_mob'           => $paxMobile,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }

            }

            if ($transaction['product_id'] == 4){

                $checkTrue = DB::table('cl_card_rep')->insert([
                    'atek_id'          => $transaction['atek_id'],
                    'txn_date'         => $transaction['txn_date'],
                    'engraved_id'      => $transaction['engraved_id'],
                    'chip_id'          => $transaction['chip_id'],
                    'stn_id'           => $transaction['stn_id'],
                    'tp_balance'       => $transaction['tp_balance'],
                    'card_sec'         => $transaction['card_sec'],
                    'card_fee'         => 0,
                    'pass_id'          => $transaction['pass_id'],
                    'product_id'       => $transaction['product_id'],
                    'pass_expiry'      => $transaction['pass_expiry'],
                    'src_stn_id'       => $transaction['src_stn_id'],
                    'des_stn_id'       => $transaction['des_stn_id'],
                    'tid'              => $transaction['tid'],
                    'eq_id'            => $transaction['eq_id'],
                    'eq_type_id'       => $transaction['eq_type_id'],
                    'pax_first_name'   => $paxFirstName,
                    'pax_last_name'    => $paxLastName,
                    'pax_mobile'       => $paxMobile,
                ]);

                if ($checkTrue){

                    DB::table('cl_status')->insert([
                        'engraved_id'        => $transaction['engravedId'],
                        'chip_id'            => $transaction['chipId'],
                        'txn_date'           => $transaction['txnDate'],
                        'pass_id'            => $transaction['passId'],
                        'product_id'         => $transaction['productID'],
                        'card_fee'           => $transaction['card_fee'],
                        'tp_balance'         => $transaction['tp_balance'],
                        'pass_expiry_date'   => $transaction['passExpiry'],
                        'src_stn_id'         => $transaction['srcStnId'],
                        'des_stn_id'         => $transaction['desStnId'],
                        'auto_topup_status'  => $autoTopUpStatus,
                        'auto_topup_amt'     => $autoTopUpAmount,
                        'bonus_points'       => $bonusPoints,
                        'is_test'            => false,
                        'pax_name'           => $paxFirstName,
                        'pax_gen_type'       => $paxGender,
                        'pax_mob'            => $paxMobile,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }

            }

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atekId'];

            $response[] = $transData;
        }

        return response([
            'status' => true,
            'trans' => $response
        ]);

    }
}

