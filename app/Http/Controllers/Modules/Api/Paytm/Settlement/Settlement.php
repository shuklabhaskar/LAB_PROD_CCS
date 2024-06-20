<?php

namespace App\Http\Controllers\Modules\Api\Paytm\Settlement;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use paytm\paytmchecksum\PaytmChecksum;

class Settlement extends Controller
{
    public function settlement()
    {

        set_time_limit(0);

        $mId        = 'Mumbai80360927499450';
        $mKey       = 'q#999da%Jr_OdPBg';
        $pageNum    = 1;
        $pageSize   = 20;

        $processDates = DB::table('ol_settlement')
            ->select(DB::raw('DISTINCT DATE(txn_date) AS txn_date'))
            ->where('is_settle', false)
            ->orderBy('txn_date', 'ASC')
            ->pluck('txn_date');

        foreach ($processDates as $date) {

            $date = '2023-10-31';

            while (true) {

                sleep(1);

                $paytmParams = [
                    "MID"                   => $mId,
                    "utrProcessedStartTime" => $date,
                    "pageNum"               => $pageNum,
                    "pageSize"              => $pageSize,
                ];

                Log::channel('paytmSettlement')->info( $date . " -> " . json_encode($paytmParams));

                $checksum = PaytmChecksum::generateSignature($paytmParams, $mKey);
                $paytmParams["checksumHash"] = $checksum;

                try {

                    $response = Http::withOptions([
                        'verify' => false,
                    ])
                        ->post('https://securegw.paytm.in/merchant-settlement-service/settlement/list', $paytmParams)
                        ->collect();

                    Log::channel('paytmSettlement')->info( $date . " -> " . json_encode($response));

                    if ($response["status"] == "FAILURE") {
                        continue;
                    }

                    if ($response['settlementListResponse']['paginatorTotalPage'] == 0) {
                        break;
                    }

                    // INCREASE PAGE NUMBER
                    $pageNum++;

                    if ($response["settlementListResponse"]["totalCount"] == 0) {
                        continue;
                    }

                    $settlementList = $response['settlementListResponse']['settlementTransactionList'];


                    foreach ($settlementList as $transaction) {

                        try {
                            DB::table('ol_settlement')
                                ->where('atek_id', '=', $transaction['ORDERID'])
                                ->where('is_settle', '=', false)
                                ->update([
                                    'bank_order_id'     => $transaction['TXNID'],
                                    'is_settle'         => true,
                                    'settlement_date'   => $transaction['SETTLED DATE'],
                                    'settlement_amt'    => $transaction['SETTLEDAMOUNT'],
                                    'mdr_amt'           => $transaction['COMMISSION'],
                                    'updated_at'        => now(),
                                    'gst'               => $transaction['GST'],
                                    'utr'               => $transaction['UTR'],
                                    'bank_name'         => $transaction['BANKNAME'],
                                    'payout'            => $transaction['PAYOUT DATE'],
                                ]);
                        } catch (QueryException $ex) {
                            Log::channel('paytmSettlement')->info('Database exception:' . $ex->getMessage());
                            return response()->json(['error' => 'Database error occurred'], 500);
                        }
                    }

                } catch (RequestException $e) {
                    Log::channel('paytmSettlement')->info($e->getMessage());
                }

            }

        }

        return "TRUE";

    }

}
