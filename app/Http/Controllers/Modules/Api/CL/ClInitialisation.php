<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PDOException;

class ClInitialisation extends Controller
{
    public function initialisation(Request $request)
    {
        /*CONVERTING IT TO MAKE IN ARRAY*/
        $transactions = $request->json()->all();
        $response     = [];

        if (!is_array($transactions)) {
            return response()->json([
                'status' => false,
                'error'  => 'Invalid request data. Expected an array of objects.'
            ]);
        }

        /* ACCESSING TRANSACTION SINGLE USING FOR EACH */
        foreach ($transactions as $transaction) {

            $transData = [];

            /* VALIDATION FOR INPUT REQUEST */
            $validator = Validator::make($request->all(), [
                '*.id'            => 'required',
                '*.chip_id'       => 'required|integer',
                '*.engraved_id'   => 'required|integer',
                '*.eq_id'         => 'required|string',
                '*.txn_date'      => 'required|date_format:Y-m-d H:i:s',
            ], [
                'id.required'           => 'The ID field is required and cannot be empty.',
                'chip_id.required'      => 'The Chip ID field is required and cannot be empty.',
                'chip_id.min'           => 'The Chip ID must be a Integer.',
                'engraved_id.required'  => 'The Engraved ID field is required and cannot be empty.',
                'engraved_id.min'       => 'The Engraved ID must be a Integer.',
                'eq_id.required'        => 'The Equipment ID field is required and cannot be empty.',
                'eq_id.string'          => 'The Equipment ID must be a string.',
            ]);

            /* IF VALIDATION FAILS */
            if ($validator->fails()) {
                $transData = [
                    'id'     => $transaction['id'],
                    'sync'   => false,
                    'error'  => $validator->errors()->first()
                ];
                $response[] = $transData;
                continue;
            }

            /* TO START THE DATABASE TRANSACTION IN THIS API */
            DB::beginTransaction();

            try {

                /* INSERTING THE REQUESTED DATA FIRST IN INITIALISATION TABLE */
                DB::table('cl_ini_data')
                    ->insert([
                        'engraved_id'   => $transaction['engraved_id'],
                        'chip_id'       => $transaction['chip_id'],
                        'txn_date'      => $transaction['txn_date'],
                        'eq_id'         => $transaction['eq_id'],
                        'card_expiry'   => $transaction['card_expiry'],
                        'created_at'    => Carbon::now(),
                    ]);

                /*DELETING REQUESTED ENGRAVED ROW IF EXISTS*/
                DB::table('cl_blacklist')
                    ->where('engraved_id', $transaction['engraved_id'])
                    ->delete();

                /*UPDATING OR INSERTING THE ROW ON BASIS OF ENGRAVED ID EXISTS IN TABLE OR NOT */
                DB::table('cl_status')
                    ->updateOrInsert(
                        ['engraved_id' => $transaction['engraved_id']], /* TO CHECK WHERE ENGRAVED ID ALREADY EXIST IN TABLE OR NOT  */
                        [
                            'chip_id'           => $transaction['chip_id'],
                            'txn_date'          => Carbon::now(),
                            'pass_id'           => 0,
                            'product_id'        => 0,
                            'card_fee'          => 0,
                            'card_sec'          => 0,
                            'sv_balance'        => 0.0,
                            'tp_balance'        => 0.0,
                            'pass_expiry'       => Carbon::now(),
                            'src_stn_id'        => 0,
                            'des_stn_id'        => 0,
                            'auto_topup_status' => 0,
                            'auto_topup_amt'    => 0,
                            'bonus_points'      => 0,
                            'is_test'           => false,
                            'pax_first_name'    => "",
                            'pax_last_name'     => "",
                            'pax_mobile'        => 0,
                            'pax_gen_type'      => 0,
                            'created_at'        => Carbon::now(),
                            'updated_at'        => Carbon::now(),
                        ]);

                /*ALL DB OPERATION COMMITED IF SUCCESSFUL*/
                DB::commit();

                $transData['id']    = $transaction['id'];
                $transData['sync']  = true;

                $response[] = $transData;

            } catch (PDOException $e) {

                /*IF ANY ERROR FOUND THAN REQUESTED TRANSACTION WILL BE ROLLBACK OR REVERTED*/
                DB::rollBack();

                /***
                 * Creating Log With Respected to Requested
                 * ID
                 * For Individual Transaction
                 */

                $transData['id']    = $transaction['id'];
                $transData['sync']  = false;
                $transData['error'] = $e->getMessage();
                Log::channel('clInitialisation')->info($e->getMessage() . $request['engraved_id']);

                $response[] = $transData;

            }

        }

        return response([
            'status' => true,
            'data'   => $response
        ]);


    }
}
