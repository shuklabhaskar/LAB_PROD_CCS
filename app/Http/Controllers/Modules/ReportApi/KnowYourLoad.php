<?php

namespace App\Http\Controllers\Modules\ReportApi;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KnowYourLoad extends Controller
{
    public function cl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date' => 'required|date_format:Y-m-d H:i:s',
            'interval' => 'required|integer',
            'station' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        $combinedCountsFormatted = [];

        $entryFrom = Carbon::parse($request->input('from_date'));
        $entryTo = Carbon::parse($request->input('to_date'));
        $interval = $request->input('interval');
        $station = $request->input('station');

        $tpEngravedIds = DB::table('cl_tp_validation')
            ->whereBetween('txn_date', [$entryFrom, $entryTo])
            ->where('stn_id', $station)
            ->where('val_type_id', 1)
            ->orderBy('stn_id', 'desc')
            ->pluck('engraved_id')
            ->toArray();

        $svEngravedIds = DB::table('cl_sv_validation')
            ->whereBetween('txn_date', [$entryFrom, $entryTo])
            ->where('stn_id', $station)
            ->where('val_type_id', 1)
            ->orderBy('stn_id', 'desc')
            ->pluck('engraved_id')
            ->toArray();

        $tpExitRecord = DB::table('cl_tp_validation')
            ->select('stn_id', DB::raw('COUNT(*) as count'))
            ->whereBetween('txn_date', [$entryFrom, $entryFrom->copy()->addMinutes($interval)])
            ->whereIn('engraved_id', $tpEngravedIds)
            ->where('val_type_id', 2)
            ->groupBy('stn_id')
            ->orderBy('stn_id', 'desc')
            ->get();

        $svExitRecord = DB::table('cl_sv_validation')
            ->select('stn_id', DB::raw('COUNT(*) as count'))
            ->whereBetween('txn_date', [$entryFrom, $entryFrom->copy()->addMinutes($interval)])
            ->whereIn('engraved_id', $svEngravedIds)
            ->where('val_type_id', 2)
            ->groupBy('stn_id')
            ->orderBy('stn_id', 'desc')
            ->get();


        $combinedCounts = [];

        foreach ($tpExitRecord as $record) {
            $stnId = $record->stn_id;
            $count = $record->count;

            if (!isset($combinedCounts[$stnId])) {
                $combinedCounts[$stnId] = 0;
            }

            $combinedCounts[$stnId] += $count;
        }

        foreach ($svExitRecord as $record) {
            $stnId = $record->stn_id;
            $count = $record->count;

            if (!isset($combinedCounts[$stnId])) {
                $combinedCounts[$stnId] = 0;
            }

            $combinedCounts[$stnId] += $count;
        }



        $sumOfCounts = 0;
        foreach ($combinedCounts as $stnId => $count) {

            $combinedCountsFormatted[] = [
                'stn_id' => $stnId,
                'count' => $count,
            ];
            $sumOfCounts += $count;

        }

        $totalCount = count($tpEngravedIds) + count($svEngravedIds);


        return response()->json([
            'status'        => true,
            'Total_Entry'   => $totalCount,
            'Total_Exit'    => $sumOfCounts,
            'data'          => $combinedCountsFormatted,
        ]);
    }

    public function ol(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date' => 'required|date_format:Y-m-d H:i:s',
            'interval' => 'required|integer',
            'station' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors(),
            ]);
        }

        $combinedCountsFormatted = [];

        $entryFrom = Carbon::parse($request->input('from_date'));
        $entryTo = Carbon::parse($request->input('to_date'));
        $interval = $request->input('interval');
        $station = $request->input('station');

        $olSvEngravedIds = DB::table('ol_sv_validation')
            ->whereBetween('txn_date', [$entryFrom, $entryTo])
            ->where('stn_id', $station)
            ->where('val_type_id', 1)
            ->orderBy('stn_id', 'desc')
            ->pluck('card_hash_no')
            ->toArray();

        $olSvExitRecord = DB::table('ol_sv_validation')
            ->select('stn_id', DB::raw('COUNT(*) as count'))
            ->whereBetween('txn_date', [$entryFrom, $entryFrom->copy()->addMinutes($interval)])
            ->whereIn('card_hash_no', $olSvEngravedIds)
            ->where('val_type_id', 2)
            ->groupBy('stn_id')
            ->orderBy('stn_id', 'desc')
            ->get();

        $combinedCounts = [];

        foreach ($olSvExitRecord as $record) {
            $stnId = $record->stn_id;
            $count = $record->count;

            if (!isset($combinedCounts[$stnId])) {
                $combinedCounts[$stnId] = 0;
            }

            $combinedCounts[$stnId] += $count;
        }

        $sumOfCounts = 0;
        foreach ($combinedCounts as $stnId => $count) {

            $combinedCountsFormatted[] = [
                'stn_id' => $stnId,
                'count' => $count,
            ];
            $sumOfCounts += $count;

        }

        $totalCount = count($olSvEngravedIds);


        return response()->json([
            'status'        => true,
            'Total_Entry'   => $totalCount,
            'Total_Exit'    => $sumOfCounts,
            'data'          => $combinedCountsFormatted,
        ]);
    }

}
