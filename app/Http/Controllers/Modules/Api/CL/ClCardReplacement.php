<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClCardReplacement extends Controller
{
    public function store(Request $request)
    {

        $transactions = json_decode($request->getContent(), true);
        $response = [];

        if ($request == [] || $request == null)
            return response([
                'status' => false,
                'error' => "please provide required data!"
            ]);

        foreach ($transactions as $transaction) {

            DB::table('cl_card_rep')->insert([
                'atek_id'       => $transaction['atekId'],
                'txn_date'      => $transaction['txnDate'],
                'engraved_id'   => $transaction['engravedId'],
                'chip_id'       => $transaction['chipId'],
                'stn_id'        => $transaction['stnId'],
                'pass_bal'      => $transaction['passBal'],
                'card_sec'      => $transaction['cardSec'],
                'pass_id'       => $transaction['passId'],
                'pass_expiry'   => $transaction['passExpiry'],
                'tid'           => $transaction['tid'],
            ]);

            $transData['is_settled'] = true;
            $transData['atek_id'] = $transaction['atekId'];

            array_push($response, $transData);
        }

        return response([
            'status' => true,
            'trans' => $response
        ]);

    }
}
