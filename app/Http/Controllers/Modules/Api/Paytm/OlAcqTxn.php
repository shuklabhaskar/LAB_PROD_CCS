<?php

namespace App\Http\Controllers\Modules\Api\Paytm;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use function Symfony\Component\Finder\size;

class OlAcqTxn extends Controller
{
    public function index()
    {

        $finalResponse = [];

        $tids = DB::table('ol_acq_txn')
            ->where('is_sync', '=', false)
            ->select('emv_tid')
            ->get();

        foreach ($tids as $tid) {

            $paytmBody = [];
            $date = Carbon::now()->toDateString();
            $time = Carbon::now()->toTimeString();
            $dateTime = $date . "T" . $time;

            $requests = DB::table('ol_acq_txn')
                ->where('emv_tid', '=', $tid->emv_tid)
                ->where('is_sync', '=', false)
                ->limit(100)
                ->get('request_json');

            foreach ($requests as $request) {
                $paytmBody[] = json_decode($request->request_json);
            }

            if (count($paytmBody) > 0){

                $response = Http::timeout(2 * 60)
                    ->withBody('{
                    "offlineSaleRequestHeader": {
                        "clientId": "FIEG0N001",
                        "version": "1.1"
                    },
                    "offlineSaleRequestBody": {
                        "requestTimestamp": "' . $dateTime . '",
                        "tid": "' . $tid->emv_tid . '",
                        "mid": "Mumbai80360927499450",
                        "offlineSaleBatch": ' . json_encode($paytmBody) . '
                    }
                }', 'application/json')
                    ->post('https://securegw.paytm.in/nos/offlineSale')->collect();

                $parsedResponse = json_decode($response);

                if ($parsedResponse->body->resultCode != "S") {
                    DB::table('ol_acq_txn')
                        ->where('is_sync', false)
                        ->where('emv_tid', '=', $tid->emv_tid)
                        ->update(['response_json' => $response]);
                } else {
                    foreach ($parsedResponse->body->offlineSaleBatchResponseList as $resList) {
                        if ($resList->resultCode == "S") {
                            DB::table('ol_acq_txn')
                                ->where('atek_id', '=', $resList->orderId)
                                ->update([
                                    'response_json' => json_encode($resList),
                                    'is_sync' => true,
                                    'bank_order_id' => "",
                                ]);
                        } else {

                            DB::table('ol_acq_txn')
                                ->where('atek_id', '=', $resList->orderId)
                                ->update([
                                    'response_json' => json_encode($resList),
                                    'is_sync' => false,
                                    'bank_order_id' => "",
                                ]);

                        }
                    }
                }

                $finalResponse[$tid->emv_tid] = $response;

            }

        }

        return $finalResponse;

    }
}
