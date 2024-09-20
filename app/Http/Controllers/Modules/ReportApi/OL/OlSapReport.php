<?php

namespace App\Http\Controllers\Modules\ReportApi\OL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OlSapReport extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date'   => 'required|date_format:Y-m-d H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors(),
            ]);
        }

        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');

        $query = "select DT, stn_id, PAY_MOD,
     sum(OL_PURSE_SALE) as OL_PURSE_SALE, sum(OL_PURSE_CANCELLATION) as OL_PURSE_CANCELLATION, sum(OL_PURSE_TOPUP) as OL_PURSE_TOPUP ,
     sum(OL_NON_REFUNDABLE_PASS_SALE) as OL_NON_REFUNDABLE_PASS_SALE, sum(OL_NON_REFUNDABLE_PASS_CANCELLATION) as OL_NON_REFUNDABLE_PASS_CANCELLATION,
     sum(OL_NON_REFUNDABLE_PASS_TOPUP) as OL_NON_REFUNDABLE_PASS_TOPUP,
	 sum(OL_REFUNDABLE_PASS_SALE) as OL_REFUNDABLE_PASS_SALE ,
	 sum(OL_REFUNDABLE_PASS_CANCELLATION) as OL_REFUNDABLE_PASS_CANCELLATION ,
	 sum(OL_REFUNDABLE_PASS_TOPUP) as OL_REFUNDABLE_PASS_TOPUP ,
	 sum(OL_REFUNDABLE_PASS_REFUND) as OL_REFUNDABLE_PASS_REFUND ,
	 sum(OL_REFUNDABLE_PASS_REFUND_CHARGE) as OL_REFUNDABLE_PASS_REFUND_CHARGE ,
	 sum(OL_PENALTY_AMOUNT) as OL_PENALTY_AMOUNT, sum(OL_GRA_AMOUNT) as OL_GRA_AMOUNT, sum(OL_NON_REFUNDABLE_DEPOSIT) as OL_NON_REFUNDABLE_DEPOSIT,
	 sum(OL_REFUNDABLE_DEPOSIT) as OL_REFUNDABLE_DEPOSIT,
     sum(OL_REFUNDABLE_DEPOSIT_REFUND) as OL_REFUNDABLE_DEPOSIT_REFUND,
     sum(OL_REFUNDABLE_DEPOSIT_REFUND_CHARGE) as OL_REFUNDABLE_DEPOSIT_REFUND_CHARGE,
	 sum(OL_REPLACEMENT_AMOUNT) as OL_REPLACEMENT_AMOUNT
     from (
     SELECT CASE WHEN To_Char(txn_date,'hh24miss')>='0'  AND To_Char(txn_date,'hh24miss')<='010959'
     THEN To_Char((txn_date)-interval '1 DAY','yyyymmdd') ELSE To_Char(txn_date,'yyyymmdd') END AS DT,
     stn_id,
     CASE pay_type_id WHEN 1 THEN 'CAS' WHEN 2 THEN 'VOC' ELSE 'N/A' end as PAY_MOD,
     coalesce(Sum(CASE WHEN pay_type_id in (1,2) and op_type_id in (11) THEN total_price END),0) as OL_PURSE_SALE,
     0  as OL_PURSE_CANCELLATION,
     0 as OL_PURSE_TOPUP,
     0 as OL_NON_REFUNDABLE_PASS_SALE,
     0 as OL_NON_REFUNDABLE_PASS_CANCELLATION,
     0 as OL_NON_REFUNDABLE_PASS_TOPUP,
     0 as OL_REFUNDABLE_PASS_SALE,
     0 as OL_REFUNDABLE_PASS_CANCELLATION,
     0 as OL_REFUNDABLE_PASS_TOPUP,
     0 as OL_REFUNDABLE_PASS_REFUND,
     0 as OL_REFUNDABLE_PASS_REFUND_CHARGE,
     0 as OL_PENALTY_AMOUNT,
     0 as OL_GRA_AMOUNT,
     0 as OL_NON_REFUNDABLE_DEPOSIT,
     0 as OL_REFUNDABLE_DEPOSIT,
     0 as OL_REFUNDABLE_DEPOSIT_REFUND,
     0 as OL_REFUNDABLE_DEPOSIT_REFUND_CHARGE,
     0 as OL_REPLACEMENT_AMOUNT
     from  ol_card_sale where
     txn_date >= ? and txn_date < ? and pay_type_id in (1,2)
     group by DT,stn_id,PAY_MOD  union all
     SELECT CASE WHEN To_Char(a.txn_date,'hh24miss')>='0'  AND To_Char(a.txn_date,'hh24miss')<='010959'
     THEN To_Char((a.txn_date)-interval '1 DAY','yyyymmdd') ELSE To_Char(a.txn_date,'yyyymmdd') END AS DT, a.stn_id,
     CASE pay_type_id WHEN 1 THEN 'CAS' WHEN 2 THEN 'VOC' ELSE 'N/A' end as PAY_MOD,
     0 as OL_PURSE_SALE,
  	 0 as OL_PURSE_CANCELLATION,
     coalesce(Sum(CASE WHEN   a.pass_id in (82) and a.pay_type_id in (1,2) and op_type_id in (3) THEN total_price END),0) as OL_PURSE_TOPUP,
   	 0 as OL_NON_REFUNDABLE_PASS_SALE,
 	 0 as OL_NON_REFUNDABLE_PASS_CANCELLATION,
     0 as OL_NON_REFUNDABLE_PASS_TOPUP,
     0 as OL_REFUNDABLE_PASS_SALE,
     0 as OL_REFUNDABLE_PASS_CANCELLATION,
     0 as OL_REFUNDABLE_PASS_TOPUP,
     0 as OL_REFUNDABLE_PASS_REFUND,
     0 as OL_REFUNDABLE_PASS_REFUND_CHARGE,
     coalesce(Sum(CASE WHEN b.pen_type_id in (24,31,32,33,34,35,36,37) and a.pay_type_id in (1,2) THEN pen_price END),0)  as OL_PENALTY_AMOUNT,
     coalesce(Sum(CASE WHEN  a.pass_id in (82) and  b.pen_type_id in (14) and a.pay_type_id in (1,2)  THEN pen_price END),0) as OL_GRA_AMOUNT,
     0 as OL_NON_REFUNDABLE_DEPOSIT,
     0 as OL_REFUNDABLE_DEPOSIT,
     0 as OL_REFUNDABLE_DEPOSIT_REFUND,
     0 as OL_REFUNDABLE_DEPOSIT_REFUND_CHARGE,
     0 as OL_REPLACEMENT_AMOUNT
     from  ol_sv_accounting a left join ol_pen_accounting b on a.ms_acc_id=b.ms_acc_id  and a.pass_id=b.pass_id where
     a.txn_date >= ? and a.txn_date < ? and a.pay_type_id in (1,2)
     group by DT,a.stn_id,PAY_MOD
     ) a
     group by DT,stn_id,PAY_MOD
     order by DT,stn_id,PAY_MOD;";

        $results = DB::select($query, [$fromDate, $toDate, $fromDate, $toDate]);

        return response()->json([
            'status' => true,
            'data' => $results,
        ]);
    }
}
