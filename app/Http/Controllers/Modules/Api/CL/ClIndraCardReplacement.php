<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClIndraCardReplacement extends Controller
{
    public function store(Request $request): Response|Application|ResponseFactory
    {

        $transactions = json_decode($request->getContent(), true);
        $response     = [];

        if ($transactions == []) {

            return response([
                'status' => false,
                'error'  => "please provide required data!"
            ]);

        }

        foreach ($transactions as $transaction) {

            /* CHECK THAT IS TEST IS NULLABLE OR NOT */
            $paxFirstName    = "";
            $paxLastName     = "";
            $paxGender       = 0;
            $paxMobile       = 1234567891;
            $autoTopUpStatus = false;
            $autoTopUpAmount = 0.0;
            $bonusPoints     = 0.0;

            if (array_key_exists("firstName", $transaction)) $paxFirstName  = $transaction['firstName'];
            if (array_key_exists("lastName", $transaction)) $paxLastName    = $transaction['lastName'];
            if (array_key_exists("pax_gen_type", $transaction)) $paxGender  = $transaction['pax_gen_type'];
            if (array_key_exists("mobileNumber", $transaction)) $paxMobile  = $transaction['mobileNumber'];
            if (array_key_exists("auto_topup_status", $transaction)) $autoTopUpStatus = $transaction['auto_topup_status'];
            if (array_key_exists("auto_topup_amt", $transaction)) $autoTopUpAmount = $transaction['auto_topup_amt'];
            if (array_key_exists("bonus_points", $transaction)) $bonusPoints = $transaction['bonus_points'];

            //DB::beginTransaction();

            try {

                $tpBalance  = 0;
                $svBalance  = 0;
                $srcStnId   = 0;
                $desStnId   = 0;

                if ($transaction['productId'] == 3) { // SV
                    $svBalance = $transaction['passBal'];
                } else {
                    $tpBalance = $transaction['passBal'];
                    $srcStnId  = $transaction['srcStnId'];
                    $desStnId  = $transaction['desStnId'];
                }

                try {
                    DB::table('cl_indra_rep')->insert([
                        'atek_id'        => $transaction['atekId'],
                        'txn_date'       => $transaction['txnDate'],
                        'engraved_id'    => $transaction['engravedId'],
                        'chip_id'        => $transaction['chipId'],
                        'stn_id'         => $transaction['stnId'],
                        'tp_balance'     => $tpBalance,
                        'sv_balance'     => $svBalance,
                        'card_sec'       => $transaction['cardSec'],
                        'card_fee'       => $transaction['cardFee'],
                        'pass_id'        => $transaction['passId'],
                        'product_id'     => $transaction['productId'],
                        'pass_expiry'    => $transaction['passExpiry'],
                        'src_stn_id'     => $srcStnId,
                        'des_stn_id'     => $desStnId,
                        'tid'            => $transaction['tid'],
                        'eq_id'          => $transaction['eqId'],
                        'eq_type_id'     => $transaction['eqTypeId'],
                        'pax_first_name' => $paxFirstName,
                        'pax_last_name'  => $paxLastName,
                        'pax_mobile'     => $paxMobile,
                    ]);
                } catch (\PDOException $e) {
                    \Log::error("FAILED TO INSERT IN CL_INTRA ERROR: " . $e->getMessage());
                }

                DB::table('cl_status')->insert([
                    'engraved_id'       => $transaction['engravedId'],
                    'chip_id'           => $transaction['chipId'],
                    'txn_date'          => $transaction['txnDate'],
                    'pass_id'           => $transaction['passId'],
                    'product_id'        => $transaction['productId'],
                    'card_fee'          => $transaction['cardFee'],
                    'card_sec'          => $transaction['cardSec'],
                    'sv_balance'        => $svBalance,
                    'tp_balance'        => $tpBalance,
                    'pass_expiry'       => $transaction['passExpiry'],
                    'src_stn_id'        => $srcStnId,
                    'des_stn_id'        => $desStnId,
                    'auto_topup_status' => $autoTopUpStatus,
                    'auto_topup_amt'    => $autoTopUpAmount,
                    'bonus_points'      => $bonusPoints,
                    'is_test'           => false,
                    'pax_first_name'    => $paxFirstName,
                    'pax_last_name'     => $paxLastName,
                    'pax_gen_type'      => $paxGender,
                    'pax_mobile'        => $paxMobile,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                $transData['is_settled'] = true;
                $transData['atek_id']    = $transaction['atekId'];
                $response[]              = $transData;

            } catch (\Exception $e) {
                $transData['is_settled'] = false;
                $transData['atek_id']    = $transaction['atekId'];
                $transData['error']      = $e->getMessage();
                $response[]              = $transData;

             //DB::rollBack();

            }

           //DB::commit();

        }

        return response([
            'status' => true,
            'trans' => $response
        ]);

    }
}

