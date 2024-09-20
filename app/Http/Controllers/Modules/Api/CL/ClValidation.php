<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;

class ClValidation extends Controller
{

    function setClTransaction(Request $request)
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

            /* CHECK THAT IS TEST IS NULLABLE OR NOT */
            $is_test = null;
            if (array_key_exists("is_test", $transaction)) $is_test = $transaction['is_test'];

            $clStatus = DB::table('cl_status')
                ->where('engraved_id', '=', $transaction['engraved_id'])
                ->first();

            try {

                /* FOR STORE VALUE PASS ONLY  */
                if ($transaction['product_id'] == 3) {

                    /* VALIDATION */
                    $validator = Validator::make($request->all(), [
                        '*.atek_id'       => 'required',
                        '*.txn_date'      => 'required',
                        '*.engraved_id'   => 'required',
                        '*.val_type_id'   => 'required|integer',
                        '*.amt_deducted'  => 'required',
                        '*.chip_balance'  => 'required',
                        '*.media_type_id' => 'required|integer',
                        '*.product_id'    => 'required|integer',
                        '*.pass_id'       => 'required',
                        '*.card_type_id'  => 'required',
                        '*.eq_id'         => 'required',
                        '*.stn_id'        => 'required|integer',
                    ]);

                    /* IF VALIDATION FAILS */
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => true,
                            'error'  => str($validator->errors())
                        ]);
                    }

                    /* FOR STORE VALUE VALIDATION */
                    $SvValidationTrue = DB::table('cl_sv_validation')->insert([
                        'atek_id'       => $transaction['atek_id'],
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'val_type_id'   => $transaction['val_type_id'],
                        'amt_deducted'  => $transaction['amt_deducted'],
                        'chip_balance'  => $transaction['chip_balance'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                        'card_type_id'  => $transaction['card_type_id'],
                        'eq_id'         => $transaction['eq_id'],
                        'stn_id'        => $transaction['stn_id'],
                        'is_test'       => $is_test,
                    ]);

                    if ($SvValidationTrue && $transaction['val_type_id'] == 2) {

                        if ($clStatus != null) {

                            $oldTxnDate = Carbon::make($clStatus->txn_date);
                            $newTxnDate = Carbon::make($transaction['txn_date']);

                            if ($newTxnDate > $oldTxnDate) {

                                $updates = [
                                    'sv_balance' => $transaction['chip_balance'],
                                    'txn_date'   => $transaction['txn_date'],
                                    'updated_at' => now()
                                ];

                                if (!empty($transaction['pass_expiry'])) {
                                    $updates['pass_expiry'] = $transaction['pass_expiry'];
                                }

                                DB::table('cl_status')
                                    ->where('engraved_id', '=', $transaction['engraved_id'])
                                    ->update($updates);

                            }

                        }else{
                            $message = "Txn Date is not available for the given engraved_id in Cl Status: {$transaction['engraved_id']}";
                            Log::channel('clAccounting')->info($message);
                        }



                    }


                } elseif ($transaction['product_id'] == 4) { /* FOR TRIP PASS ONLY */

                    /* VALIDATION */
                    $validator = Validator::make($request->all(), [
                        '*.atek_id'         => 'required',
                        '*.txn_date'        => 'required',
                        '*.engraved_id'     => 'required',
                        '*.val_type_id'     => 'required',
                        '*.trip_deducted'   => 'required',
                        '*.trip_balance'    => 'required',
                        '*.media_type_id'   => 'required',
                        '*.product_id'      => 'required',
                        '*.pass_id'         => 'required',
                        '*.card_type_id'    => 'required',
                        '*.eq_id'           => 'required',
                        '*.stn_id'          => 'required',
                    ]);

                    /* IF VALIDATION FAILS */
                    if ($validator->fails()) {
                        return response()->json([
                            'status' => true,
                            'error'  => str($validator->errors())
                        ]);
                    }

                    /* FOR TRIP PASS VALIDATION */
                    $TpValidationTrue = DB::table('cl_tp_validation')->insert([
                        'atek_id'       => $transaction['atek_id'],
                        'txn_date'      => $transaction['txn_date'],
                        'engraved_id'   => $transaction['engraved_id'],
                        'val_type_id'   => $transaction['val_type_id'],
                        'trip_deducted' => $transaction['trip_deducted'],
                        'trip_balance'  => $transaction['trip_balance'],
                        'media_type_id' => $transaction['media_type_id'],
                        'product_id'    => $transaction['product_id'],
                        'pass_id'       => $transaction['pass_id'],
                        'card_type_id'  => $transaction['card_type_id'],
                        'eq_id'         => $transaction['eq_id'],
                        'stn_id'        => $transaction['stn_id'],
                        'is_test'       => $is_test,
                    ]);

                    if ($TpValidationTrue) {

                        if ($clStatus != null) {

                            $oldTxnDate = Carbon::make($clStatus->txn_date);
                            $newTxnDate = Carbon::make($transaction['txn_date']);

                            if ($newTxnDate > $oldTxnDate) {

                                $updates = [
                                    'tp_balance'    => $transaction['trip_balance'],
                                    'txn_date'      => $transaction['txn_date'],
                                    'updated_at'    => now()
                                ];

                                if (!empty($transaction['pass_expiry'])) {
                                    $updates['pass_expiry'] = $transaction['pass_expiry'];
                                }

                                DB::table('cl_status')
                                    ->where('engraved_id', '=', $transaction['engraved_id'])
                                    ->update($updates);
                            }

                        }else{
                            $message = "Txn Date is not available for the given engraved_id in Cl Status: {$transaction['engraved_id']}";
                            Log::channel('clAccounting')->info($message);
                        }
                    }


                } else {

                    $message = "Invalid Product Id Provided";
                    Log::channel('clValidation')->info($message);

                    return response([
                        'status' => false,
                        'trans'  => "Invalid Product Id !"
                    ]);
                }

                $transData['is_settled'] = true;
                $transData['atek_id'] = $transaction ['atek_id'];

                $response[] = $transData;

            } catch
            (PDOException $e) {

                /* IF COLUMN IDENTITY FOUND AS ERROR */
                if ($e->getCode() == 23505) { /* 23505 IS ERROR CODE FROM POSTGRESQL */
                    $transData['is_settled'] = true;
                } else {
                    $transData['is_settled'] = false;
                }

                $transData['atek_id'] = $transaction['atek_id'];
                $transData['error']   = $e->getMessage();
                Log::channel('clValidation')->info($e->getMessage());

                $response[] = $transData;

            }

        }

        return response([
            'status' => true,
            'trans' => $response
        ]);

    }

}
