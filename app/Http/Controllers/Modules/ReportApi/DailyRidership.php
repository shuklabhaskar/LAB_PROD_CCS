<?php

namespace App\Http\Controllers\Modules\ReportApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PDOException;

class DailyRidership extends Controller
{
    public function index(Request $request){


        $from = $request->input('from_date');
        $to   = $request->input('to_date');

        /* VALIDATION */
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d H:i:s',
            'to_date'   => 'required|date_format:Y-m-d H:i:s',
        ]);

        /* IF VALIDATION FAILS */
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error'  => $validator->errors()
            ]);
        } else {

            /* CONVERTING DATE TO LARAVEL DATE */
            $convertedFrom  = Carbon::make($from);
            $convertedTo    = Carbon::make($to);

            $entries = [];

            /* TO GET NUMBER OF STATION AND STATION NAME */
            $stations    = DB::table('station_inventory')->select('stn_name','stn_id')->orderBy('stn_id','ASC')->get();

            /* TOTAL COUNT OF ENTRY OVER ALL STATIONS */
            $total_count = DB::table('ol_sv_validation')
                ->where('val_type_id','=',1)
                ->where('pass_id','=',82)
                ->whereBetween(DB::raw('(txn_date)'), [$convertedFrom, $convertedTo])
                ->count();

            $totalPpblEntryData = $Stations = DB::table('ol_sv_validation as sv')
                ->whereBetween(DB::raw('(txn_date)'), [$convertedFrom, $convertedTo])
                ->where('sv.val_type_id','=',1)
                ->where('sv.pass_id','=',82)
                ->where('card_mask_no','LIKE','8173'.'%')
                ->count();

            $totalSbiEntryData = $Stations = DB::table('ol_sv_validation as sv')
                ->whereBetween(DB::raw('(txn_date)'), [$convertedFrom, $convertedTo])
                ->where('sv.val_type_id','=',1)
                ->where('sv.pass_id','=',82)
                ->where('card_mask_no','LIKE','8174'.'%')
                ->count();

            $totalOtherEntryData = $Stations = DB::table('ol_sv_validation as sv')
                ->whereBetween(DB::raw('(txn_date)'), [$convertedFrom, $convertedTo])
                ->where('sv.val_type_id','=',1)
                ->where('sv.pass_id','=',82)
                ->where('card_mask_no','NOT LIKE','8173'.'%')
                ->where('card_mask_no','NOT LIKE','8174'.'%')
                ->count();

            if ($total_count!==0){

                foreach ($stations as $stn_data){

                    try {

                        /* NUMBER OF ENTRY COUNT */
                        $ppblEntryData = $Stations = DB::table('ol_sv_validation as sv')
                            ->join('station_inventory as stn','stn.stn_id','=','sv.stn_id')
                            ->whereBetween(DB::raw('(txn_date)'), [$convertedFrom, $convertedTo])
                            ->where('sv.stn_id','=',$stn_data->stn_id)
                            ->where('sv.val_type_id','=',1)
                            ->where('sv.pass_id','=',82)
                            ->orderBy('stn.stn_id','ASC')
                            ->where('card_mask_no','LIKE','8173'.'%')
                            ->select(['stn.stn_name'])
                            ->count();

                        $sbiEntryData = $Stations = DB::table('ol_sv_validation as sv')
                            ->join('station_inventory as stn','stn.stn_id','=','sv.stn_id')
                            ->whereBetween(DB::raw('(txn_date)'), [$convertedFrom, $convertedTo])
                            ->where('sv.stn_id','=',$stn_data->stn_id)
                            ->where('sv.val_type_id','=',1)
                            ->where('sv.pass_id','=',82)
                            ->orderBy('stn.stn_id','ASC')
                            ->where('card_mask_no','LIKE','8174'.'%')
                            ->select(['stn.stn_name'])
                            ->count();

                        $otherEntryData = $Stations = DB::table('ol_sv_validation as sv')
                            ->join('station_inventory as stn','stn.stn_id','=','sv.stn_id')
                            ->whereBetween(DB::raw('(txn_date)'), [$convertedFrom, $convertedTo])
                            ->where('sv.stn_id','=',$stn_data->stn_id)
                            ->where('sv.val_type_id','=',1)
                            ->where('sv.pass_id','=',82)
                            ->where('card_mask_no','NOT LIKE','8173'.'%')
                            ->where('card_mask_no','NOT LIKE','8174'.'%')
                            ->orderBy('stn.stn_id','ASC')
                            ->select(['stn.stn_name'])
                            ->count();

                        $data['station_id']         = $stn_data->stn_id;
                        $data['ppbl_entry_count']   = $ppblEntryData;
                        $data['sbi_entry_count']    = $sbiEntryData;
                        $data['other_entry_count']  = $otherEntryData;

                        $entries[] = $data;


                    }catch (PDOException $e){

                        $data['success'] = false;
                        $data['message'] = "no entry found";

                        $entries[] = $data;
                    }

                }

            }else{
                return response([
                    'status'  => 'true',
                    'message' => "NO Entry Found For This Time Period",
                ]);
            }

            return response([
                'status'                     => true,
                'total_ppbl_entry_count'     => $totalPpblEntryData,
                'total_sbi_entry_count'      => $totalSbiEntryData,
                'total_other_entry_count'    => $totalOtherEntryData,
                'total_entry_count'          => $total_count,
                'data'                       => $entries,
            ]);

        }
    }
}
