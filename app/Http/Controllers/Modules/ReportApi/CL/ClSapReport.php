<?php

namespace App\Http\Controllers\Modules\ReportApi\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClSapReport extends Controller
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
                'error' => $validator->errors(),
            ]);
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $query = "select DT, STN, PAY_MOD,
        sum(CST_SAL) as CST_SAL , sum(CSC_REF_PUR_SAL) as CSC_REF_PUR_SAL, sum(CSC_NON_REF_PAS_SAL) as CSC_NON_REF_PAS_SAL, sum(CSC_REF_PAS_SAL) as CSC_REF_PAS_SAL , (sum(CST_REF) + sum(QR_CST_REF_CHG)) as CST_REF, (sum(CSC_REF_PUR_REF) + sum(QR_CSC_REF_CHG)) as CSC_REF_PUR_REF,
    sum(CSC_REF_PASS_REF) as CSC_REF_PASS_REF, sum(CST_CAN) as CST_CAN, sum(CSC_REF_PUR_CAN) as CSC_REF_PUR_CAN, sum(CSC_NON_REF_PAS_CAN) as CSC_NON_REF_PAS_CAN , sum(CSC_REF_PAS_CAN) as CSC_REF_PAS_CAN , sum(CST_GRA) as CST_GRA, sum(CSC_GRA) as CSC_GRA, sum(CST_PEN) as CST_PEN, sum(CSC_PEN) as CSC_PEN,
    sum(CSC_REF_PUR_REL) as CSC_REF_PUR_REL,
    sum(CSC_NON_REF_PAS_REL) as CSC_NON_REF_PAS_REL, sum(CSC_REF_PAS_REL) as CSC_REF_PAS_REL, sum(CSC_DEP) as CSC_DEP, (sum(CSC_DEP_REF) + sum(QR_CSC_DEP_REF_CHG)) as CSC_DEP_REF, sum(CSC_SUP) as CSC_SUP, sum(CSC_REP) as CSC_REP, sum(A) as A, sum(B) as B, sum(C) as C, sum(D) as D, sum(E) as E, sum(F) as F, sum(G) as G, sum(H) as H,
    sum(I) as I, sum(J) as J, sum(K) as K,
    sum(QR_CST_REF_CHG) as QR_CST_REF_CHG, sum(QR_CSC_REF_CHG) as QR_CSC_REF_CHG, sum(QR_CSC_DEP_REF_CHG) as QR_CSC_DEP_REF_CHG, sum(AFC_CST_REF_CHG) as AFC_CST_REF_CHG, sum(AFC_CSC_REF_CHG) as AFC_CSC_REF_CHG, sum(AFC_CSC_DEP_REF_CHG) as AFC_CSC_DEP_REF_CHG, sum(AFC_BON) as AFC_BON,
        (sum(QR_BON) - sum(QR_BON_CAN)) QR_BON, sum(AFC_CAB) as AFC_CAB, sum(QR_CAB) as QR_CAB,
    (sum(CST_SAL) - sum(CST_REF) - sum(CST_CAN) + sum(CST_GRA) + sum(CST_PEN) +
         sum(CSC_REF_PUR_SAL) + sum(CSC_NON_REF_PAS_SAL) - sum(CSC_REF_PUR_REF) - sum(CSC_REF_PUR_CAN) - sum(CSC_NON_REF_PAS_CAN) + sum(CSC_GRA) + sum(CSC_PEN) +
     sum(CSC_REF_PUR_REL) + sum(CSC_NON_REF_PAS_REL) + sum(CSC_DEP) - sum(CSC_DEP_REF) + sum(CSC_REP)) TOT_REV1,
    coalesce (sum(SALE),0) - coalesce(sum(REFUND),0) TOT_REV2
    from
        (
     SELECT CASE WHEN To_Char(a.txn_date,'hh24miss')>='0'  AND To_Char(a.txn_date,'hh24miss')<='010959'
    THEN To_Char((a.txn_date)-interval '1 DAY','yyyymmdd') ELSE To_Char(a.txn_date,'yyyymmdd') END AS DT, a.stn_id STN,
    CASE pay_type_id WHEN 1 THEN 'CAS' WHEN 2 THEN 'VOC' ELSE 'N/A' end as PAY_MOD,
        0 as CST_SAL,
    coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (1) THEN pass_price END),0) as CSC_REF_PUR_SAL,
    coalesce(Sum(CASE WHEN   a.pass_id in (23,63,24,64) and op_type_id in (1) THEN pass_price END),0)  as CSC_NON_REF_PAS_SAL,
    0 as CSC_REF_PAS_SAL,
   0 as CST_REF,
     coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (6) THEN pass_price END),0) as CSC_REF_PUR_REF,
     0 as CSC_REF_PASS_REF,
    0 as CST_CAN,
     coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (2,4) THEN pass_price END),0) as CSC_REF_PUR_CAN,
     coalesce(Sum(CASE WHEN   a.pass_id in (23,63,24,64) and op_type_id in (2,4) THEN pass_price END),0) as CSC_NON_REF_PAS_CAN,
     0 as CSC_REF_PAS_CAN,
    0 as CST_GRA,
    coalesce(Sum(CASE WHEN  a.pass_id in (23,63,24,64) and b.pen_type_id in (14)  THEN pen_price END),0) as CSC_GRA,
    0 as CST_PEN,
    coalesce(Sum(CASE when a.pass_id in (23,63,24,64) and b.pen_type_id in (24,31,32,33,34,35,36,37) THEN pen_price END),0) as CSC_PEN,
    coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (3) THEN pass_price END),0) as CSC_REF_PUR_REL,
    coalesce(Sum(CASE WHEN   a.pass_id in (23,63,24,64) and op_type_id in (3) THEN pass_price END),0) as CSC_NON_REF_PAS_REL,
    0 as CSC_REF_PAS_REL,
    coalesce(Sum(CASE WHEN  a.pass_id in (23,63) and op_type_id in (1) THEN card_sec + card_fee END),0) as CSC_DEP,
    coalesce(Sum(CASE WHEN   a.pass_id in (23,63) and op_type_id in (6) THEN card_sec + card_fee END),0) as CSC_DEP_REF,
    0 as CSC_SUP,
    coalesce(Sum(CASE WHEN   a.pass_id in (23,63,24,64) and op_type_id in (11,12,13) THEN card_sec + card_fee + processing_fee END),0) as CSC_REP,
    0 as A, 0 as B, 0 as C,
    coalesce(Sum(CASE WHEN  a.pass_id in (24,64) and op_type_id in (1) THEN card_sec + card_fee END),0) as D,
    0 as E, 0 as F, 0 as G, 0 as H, 0 as I, 0 as J, 0 as K,
    0 as QR_CST_REF_CHG,
        0 as QR_CSC_REF_CHG, 0 as QR_CSC_DEP_REF_CHG, 0 as AFC_CST_REF_CHG, 0 as AFC_CSC_REF_CHG, 0 as AFC_CSC_DEP_REF_CHG,
        0 as AFC_BON, 0 as QR_BON, 0 as QR_BON_CAN, 0 as AFC_CAB, 0 as QR_CAB,
        coalesce(Sum(CASE WHEN    a.pass_id in (23,63,24,64) and op_type_id not in (6)   THEN coalesce(total_price,0)  END),0)  SALE,
        coalesce(Sum(CASE WHEN   a.pass_id in (23,63,24,64) and op_type_id in (2,4,6)   THEN coalesce(total_price,0) END),0)  REFUND
        from  cl_tp_accounting a left join cl_pen_accounting b on a.cl_acc_id=b.cl_acc_id and a.pass_id=b.pass_id where
         a.txn_date >= ? and a.txn_date < ?
    group by DT, STN, PAY_MOD

    union all

 SELECT CASE WHEN To_Char(a.txn_date,'hh24miss')>='0'  AND To_Char(a.txn_date,'hh24miss')<='010959'
    THEN To_Char((a.txn_date)-interval '1 DAY','yyyymmdd') ELSE To_Char(a.txn_date,'yyyymmdd') END AS DT, a.stn_id STN,
    CASE pay_type_id WHEN 1 THEN 'CAS' WHEN 2 THEN 'VOC' ELSE 'N/A' end as PAY_MOD,
        0 as CST_SAL,
    coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (1) THEN pass_price END),0) as CSC_REF_PUR_SAL,
    coalesce(Sum(CASE WHEN   a.pass_id in (23,63,24,64) and op_type_id in (1) THEN pass_price END),0)  as CSC_NON_REF_PAS_SAL,
    0 as CSC_REF_PAS_SAL,
   0 as CST_REF,
     coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (6) THEN pass_price + pass_ref_chr END),0) as CSC_REF_PUR_REF,
     0 as CSC_REF_PASS_REF,
    0 as CST_CAN,
     coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (2,4) THEN pass_price END),0) as CSC_REF_PUR_CAN,
     coalesce(Sum(CASE WHEN   a.pass_id in (23,63,24,64) and op_type_id in (2,4) THEN pass_price END),0) as CSC_NON_REF_PAS_CAN,
     0 as CSC_REF_PAS_CAN,
    0 as CST_GRA,
    coalesce(Sum(CASE WHEN  a.pass_id in (73,83,84) and  b.pen_type_id in (14)  THEN pen_price END),0) as CSC_GRA,
    0 as CST_PEN,
    coalesce(Sum(CASE WHEN b.pen_type_id in (24,31,32,33,34,35,36,37) THEN pen_price END),0) as CSC_PEN,
    coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (3) THEN pass_price END),0) as CSC_REF_PUR_REL,
    coalesce(Sum(CASE WHEN   a.pass_id in (23,63,24,64) and op_type_id in (3) THEN pass_price END),0) as CSC_NON_REF_PAS_REL,
    0 as CSC_REF_PAS_REL,
    coalesce(Sum(CASE WHEN  a.pass_id in (73,83) and  op_type_id in (1) THEN card_sec + card_fee END),0) as CSC_DEP,
    coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84)  and op_type_id in (6) THEN card_sec + card_fee END),0) as CSC_DEP_REF,
    0 as CSC_SUP,
    coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84)  and op_type_id in (11,12,13) THEN card_sec + card_fee + processing_fee END),0) as CSC_REP,
    0 as A, 0 as B, 0 as C,
    coalesce(Sum(CASE WHEN  a.pass_id in (84) and  op_type_id in (1) THEN card_sec + card_fee END),0) as D,
    0 as E, 0 as F, 0 as G, 0 as H, 0 as I, 0 as J, 0 as K,
    0 as QR_CST_REF_CHG,
        0 as QR_CSC_REF_CHG, 0 as QR_CSC_DEP_REF_CHG, 0 as AFC_CST_REF_CHG,
        coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (6) THEN pass_ref_chr END),0) as AFC_CSC_REF_CHG,
        0 as AFC_CSC_DEP_REF_CHG,
        0 as AFC_BON, 0 as QR_BON, 0 as QR_BON_CAN, 0 as AFC_CAB, 0 as QR_CAB,
        coalesce(Sum(CASE WHEN    a.pass_id in (73,83,84) and op_type_id not in (6)   THEN coalesce(total_price,0)  END),0)  SALE,
        coalesce(Sum(CASE WHEN   a.pass_id in (73,83,84) and op_type_id in (2,4,6)   THEN coalesce(total_price,0) END),0)  REFUND
        from  cl_sv_accounting a left join cl_pen_accounting b on a.cl_acc_id=b.cl_acc_id and a.pass_id=b.pass_id where
         a.txn_date >= ? and a.txn_date < ?
    group by DT, STN, PAY_MOD

    ) a
    group by DT,STN,PAY_MOD";

        $results = DB::select($query, [$fromDate, $toDate, $fromDate, $toDate]);


        return response([
            'status'=>true,
            'data'=>$results
        ]);

    }

}

