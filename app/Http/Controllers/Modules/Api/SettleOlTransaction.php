<?php

namespace App\Http\Controllers\Modules\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PDOException;
use Validator;

class SettleOlTransaction extends Controller
{
    public function setOlTransaction(Request $request)
    {
        $data = $request->getContent();
        $dataArray = json_decode($data);
        $trans = [];

        if (!is_null($dataArray)) {

            foreach ($dataArray as $transaction) {

                $oldTrans = DB::table('ol_sv_validation')
                    ->where('atek_id', '=', $transaction->atek_id)
                    ->first();

                if (is_null($oldTrans)) {

                    try {

                        $afcJson    = json_decode($transaction->afc_json);
                        $bankJson   = json_decode($transaction->bank_json);

                        /* transaction date converted to timestamp format */
                        $epochTime = $transaction->txn_date;
                        /* $convertedDate = date("Y-m-d H:i:s", substr($epochTime, 0, 10)); */
                        $convertedDate = Carbon::make($epochTime);

                        $transData = [];

                        /* CHECKING ATEK ID ALREADY EXITS OR NOT */
                        if (DB::table('ol_sv_validation')->where('atek_id', '=', $transaction->atek_id)->count() > 0 && DB::table('ol_acq_txn')->where('atek_id', '=', $transaction->atek_id)->count() > 0 && DB::table('ol_settlement')->where('atek_id', '=', $transaction->atek_id)->count() > 0) {
                            $transData['is_settled'] = true;
                            $transData['atek_id'] = $transaction->atek_id;

                            $trans[] = $transData;

                        } else {

                            /* FOR SV VALIDATION */
                            DB::table("ol_sv_validation")->insert([
                                'atek_id'       => $transaction->atek_id,
                                'txn_date'      => $convertedDate,
                                'card_mask_no'  => $afcJson->card_mask_number,
                                'card_hash_no'  => $afcJson->card_hash_number,
                                'pay_scheme'    => $afcJson->pay_scheme,
                                'val_type_id'   => $transaction->val_type_id,
                                'amt_deducted'  => $afcJson->amt_deducted,
                                'media_type_id' => $transaction->media_type_id,
                                'product_id'    => $transaction->product_id,
                                'pass_id'       => $transaction->pass_id,
                                'eq_id'         => $transaction->eq_id,
                                'chip_balance'  => $afcJson->chip_balance,
                                'stn_id'        => $transaction->stn_id,
                                'merchant_id'   => $afcJson->merchant_id,
                                'terminal_id'   => $bankJson->body->tid,
                                'created_at'    => Carbon::now(),
                            ]);

                            if ($bankJson->body->amount != 0) {

                                /* DATA INSERTING IN ACC DATA TABLE */
                                DB::table("ol_acq_txn")->insert([
                                    'atek_id'      => $transaction->atek_id,
                                    'txn_date'     => $convertedDate,
                                    'request_json' => json_encode($bankJson),
                                    'stn_id'       => $transaction->stn_id,
                                    'eq_id'        => $transaction->eq_id,
                                    'emv_tid'      => $bankJson->body->tid,
                                    'amount'       => $afcJson->amt_deducted,
                                    'created_at'   => Carbon::now(),
                                ]);

                                /* DATA INSERTING FOR OL SETTLEMENT TABLE */
                                DB::table("ol_settlement")->insert([
                                    'atek_id'      => $transaction->atek_id,
                                    'txn_date'     => $convertedDate,
                                    'amount'       => $bankJson->body->amount/100,
                                    'eq_id'        => $transaction->eq_id,
                                    'created_at'   => Carbon::now(),
                                ]);
                            }

                            $transData['is_settled'] = true;
                            $transData['atek_id']    = $transaction->atek_id;

                            $trans[] = $transData;

                        }


                    } catch (PDOException $e) {

                        $transData['is_settled'] = false;
                        $transData['atek_id']    = $transaction->atek_id;
                        $transData['message']    = $e->getMessage();

                        array_push($trans, $transData);
                    }

                } else {
                    $transData = [];
                    $transData['is_settled'] = true;
                    $transData['atek_id'] = $oldTrans->atek_id;
                    $trans[] = $transData;
                }

            }

            return response([
                    'status' => true,
                    'trans' => $trans,
                ]
            );

        }

    }

}
