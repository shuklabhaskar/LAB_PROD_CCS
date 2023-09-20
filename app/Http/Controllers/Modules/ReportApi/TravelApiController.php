<?php

namespace App\Http\Controllers\Modules\ReportApi;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;


class TravelApiController extends Controller
{
    public function getReport(Request $request): Response|Application|ResponseFactory
    {
        $request->validate([
            'startDate' => 'required|date_format:Y-m-d',
            'endDate' => 'required|date_format:Y-m-d'
        ]);

        try {
            $data = DB::table('ol_sv_validation')
                ->where('txn_date', '>=', $request->input('startDate') . " 01:10:00")
                ->where('txn_date', '<=', $request->input('endDate') . " 01:10:00")
                ->get(['txn_date', 'card_hash_no', 'val_type_id', 'stn_id']);
        } catch (\PDOException $e) {
            return Response([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }

        if (is_null($data)) {
            return Response([
                'status' => false,
                'error' => "No records found !"
            ]);
        }

        return Response([
            'status' => true,
            'records' => $data
        ]);

    }
}
