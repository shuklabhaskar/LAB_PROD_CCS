<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClSapReport extends Controller
{
    public function index(Request $request)
    {
        // Get station ID from the request
        $stnID = $request->input('stn_id');

        // VALIDATION
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d',
        ]);

        // IF VALIDATION FAILS
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);
        }

        // VALIDATED REQUEST VALUES
        try {
            $from = Carbon::parse($request->input('from_date'));
            $to = Carbon::parse($request->input('to_date'));
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Invalid date format. Please provide dates in the format Y-m-d.',
            ]);
        }

        // Retrieve station name from the database
        $stnName = DB::table('station_inventory')->where('stn_id', $stnID)->value('stn_name');

        // Generate array of dates and nested data between $from and $to
        $datesInBetween = [];
        if ($from instanceof Carbon && $to instanceof Carbon) {
            $currentDate = $from->copy();

            while ($currentDate->lte($to)) {
                $date = $currentDate->toDateString();
                $datesInBetween[$date]['cas'] = $this->getEmptyDataArray();
                $datesInBetween[$date]['voc'] = $this->getEmptyDataArray();

                $currentDate = $currentDate->copy()->addDay();
            }
        } else {
            return response()->json([
                'status' => false,
                'error' => 'Invalid date format. Please provide dates in the format Y-m-d.',
            ]);
        }

        // RETURN SUCCESSFUL RESPONSE WITH STATION NAME AND DATES IN BETWEEN
        return response()->json([
            'status' => true,
            'station' => $stnName,
            'data' => $datesInBetween,
        ]);
    }

// Helper function to get an empty data array
    // Helper function to get an empty data array
    private function getEmptyDataArray()
    {
        return array(
            "CST_SALE_AMOUNT" => 0,
            "CSC_REFUNDABLE_PURSE_SALE" => 0,
            "CSC_NON_REFUNDABLE_PASS_SALE" => 0,
            "CSC_REFUNDABLE_PASS_SALE" => 0,
            "CST_REFUND" => 0,
            "CSC_REFUNDABLE_PURSE_REFUND" => 0,
            "CSC_REFUNDABLE_PASS_REFUND" => 0,
            "CST_CANCELLATION" => 0,
            "CSC_REFUNDABLE_PURSE_CANCELLATION" => 0,
            "CSC_NON_REFUNDABLE_PASS_CANCELLATION" => 0,
            "CSC_REFUNDABLE_PASS_CANCELLATION" => 0,
            "CST_GRA_AMOUNT" => 0,
            "CSC_GRA_AMOUNT" => 0,
            "CST_PENALTY_AMOUNT" => 0,
            "CSC_PENALTY_AMOUNT" => 0,
            "CSC_REFUNDABLE_PURSE_TOPUP" => 0,
            "CSC_NON_REFUNDABLE_PASS_TOPUP" => 0,
            "CSC_REFUNDABLE_PASS_TOPUP" => 0,
            "CSC_DEPOSIT" => 0,
            "CSC_DEPOSIT_REFUND" => 0,
            "CSC_SUPPORT_PRICE" => 0,
            "CSC_REPLACEMENT_AMOUNT" => 0,
            "DAILY_TAX_1" => 0,
            "DAILY_TAX_2" => 0,
            "DAILY_TAX_3" => 0,
            "DAILY_TAX_4" => 0,
            "DAILY_TAX_1_REFUND" => 0,
            "DAILY_TAX_2_REFUND" => 0,
            "DAILY_TAX_3_REFUND" => 0,
            "DAILY_TAX_4_REFUND" => 0,
            "COMP_INCOMP" => 0,
            "VERSION_NO" => 0,
            "PROCESS_TYPE" => 0,
        );
    }


}

