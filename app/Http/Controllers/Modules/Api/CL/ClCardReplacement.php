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
            $paxLastName = "";
            $paxFirstName = "";
            $PaxMobile = "1234567891";

            if(array_key_exists("paxLastName", $transaction))  $paxLastName = $transaction['paxLastName'];
            if(array_key_exists("paxFirstName", $transaction))  $paxLastName = $transaction['paxFirstName'];
            if(array_key_exists("paxMobile", $transaction))  $paxLastName = $transaction['paxMobile'];

            DB::table('cl_card_rep')->insert([
                'atek_id'           => $transaction['atekId'],
                'txn_date'          => $transaction['txnDate'],
                'engraved_id'       => $transaction['engravedId'],
                'chip_id'           => $transaction['chipId'],
                'stn_id'            => $transaction['stnId'],
                'pass_bal'          => $transaction['passBal'],
                'card_sec'          => $transaction['cardSec'],
                'pass_id'           => $transaction['passId'],
                'pass_expiry'       => $transaction['passExpiry'],
                'tid'               => $transaction['tid'],
                'pax_first_name'    => $paxFirstName,
                'pax_last_name'     => $paxLastName,
                'pax_mobile'        => $PaxMobile,
            ]);

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

