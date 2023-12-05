<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CardDetail extends Controller
{
    public function cardDetailUsingMobileNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|min:10|max:10',
        ]);

        /* IF VALIDATION FAILS */
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        $cardDetail = DB::table('cl_status')
            ->where('pax_mobile', '=', $request->mobile_number)
            ->select([
                'pax_first_name as first_name',
                'pax_last_name as last_name',
                'pax_mobile as mobile_number',
                'engraved_id as card_number'
            ])
            ->first();

        /* IF USER DATA NOT AVAILABLE */
        if ($cardDetail === null) {
            return response()->json([
                'status' => false,
                'error' => 'Sorry, Customer data not available!',
            ]);
        }

        return response([
            'status' => true,
            'data' => $cardDetail,
        ]);
    }

    public function cardDetailUsingCardNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|integer',
        ]);

        /* IF VALIDATION FAILS */
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        $cardDetail = DB::table('cl_status')
            ->where('engraved_id', '=', $request->card_number)
            ->select([
                'pax_first_name as first_name',
                'pax_last_name as last_name',
                'pax_mobile as mobile_number',
                'engraved_id as card_number'
            ])
            ->first();

        /* IF USER DATA NOT AVAILABLE */
        if ($cardDetail === null) {
            return response()->json([
                'status' => false,
                'error' => 'Sorry, Customer data not available!',
            ]);
        }

        return response([
            'status' => true,
            'data' => $cardDetail,
        ]);
    }


}
