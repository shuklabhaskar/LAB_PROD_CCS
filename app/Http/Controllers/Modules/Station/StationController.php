<?php

namespace App\Http\Controllers\Modules\Station;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StationController extends Controller
{
    /*INDEX PAGE DATA*/
    function index()
    {
        $Stations = DB::table('station_inventory')
            ->join('ms_company_inv', 'ms_company_inv.company_id', '=', 'station_inventory.company_id')
            ->join('ms_line_inventory', 'ms_line_inventory.line_id', '=', 'station_inventory.line_id')
            ->select(['station_inventory.*', 'ms_company_inv.company_name', 'ms_line_inventory.line_name'])
            ->orderBy('stn_inv_id','ASC')
            ->get();

        return Inertia::render('Station/Index', [
            'Stations' => $Stations
        ]);
    }

    /*MASTER DATA*/
    function create()
    {
        $Companies      = DB::table('ms_company_inv')->get();
        $Lines          = DB::table('ms_line_inventory')->get();

        return Inertia::render('Station/Create', [
            'Companies'     => $Companies,
            'Lines'         => $Lines,
        ]);
    }

    /*INSERTING STATION DATA*/
    function store(Request $request)
    {
        $data = $request->validate([
            'stn_name'          => 'required',
            'status'            => 'required|boolean',
            'line_id'           => 'required|integer',
            'company_id'        => 'required|integer',
            'stn_short_name'    => 'required',
            'stn_national_lang' => 'required',
            'stn_regional_lang' => 'required',
            'cord_x'            => 'required',
            'cord_y'            => 'required',
            'start_date'        => 'required',
        ]);

        $newId = DB::table('station_inventory')->orderBy('stn_inv_id', 'desc')->first('stn_inv_id');
        $stnID = ($newId != null) ? $newId->stn_inv_id + 1 : 1;

        DB::table('station_inventory')->insert([
            'stn_id'            => $stnID,
            'stn_name'          => $request->stn_name,
            'description'       => $request->description,
            'company_id'        => $request->company_id,
            'status'            => $request->status,
            'line_id'           => $request->line_id,
            'stn_short_name'    => $request->stn_short_name,
            'stn_national_lang' => $request->stn_national_lang,
            'stn_regional_lang' => $request->stn_regional_lang,
            'cord_x'            => $request->cord_x,
            'cord_y'            => $request->cord_y,
            'start_date'        => $request->start_date,
            'end_date'          => $request->end_date,
            'created_date'      => now(),
        ]);

        $this->insertJson($stnID);


        return redirect()
            ->to('stations')
            ->with([
                'message' => $request->stn_name. 'STATION CREATED SUCCESSFULLY.'
            ]);
    }

    /*SHOWING EDITABLE DATA*/
    function edit($id)
    {
        $Companies  = DB::table('ms_company_inv')->get();
        $Lines      = DB::table('ms_line_inventory')->get();
        $Station    = DB::table('station_inventory')->where('stn_inv_id', '=', $id)->first();

        return Inertia::render('Station/Edit', [
            'Companies' => $Companies,
            'Lines'     => $Lines,
            'Station'   => $Station
        ]);
    }

    /*UPDATING STATION DATA*/
    function update(Request $request, $id)
    {
        $data = $request->validate([
            'stn_name'          => 'required',
            'status'            => 'required|boolean|min:0',
            'line_id'           => 'required|integer|min:0',
            'company_id'        => 'required|integer|min:0',
            'stn_short_name'    => 'required',
            'stn_national_lang' => 'required',
            'stn_regional_lang' => 'required',
            'cord_x'            => 'required',
            'cord_y'            => 'required',
            'start_date'        => 'required',
        ]);

        DB::table('station_inventory')->where('stn_id', '=', $id)
            ->update([
                'stn_name'          => $request->stn_name,
                'description'       => $request->description,
                'company_id'        => $request->company_id,
                'status'            => $request->status,
                'line_id'           => $request->line_id,
                'stn_short_name'    => $request->stn_short_name,
                'stn_national_lang' => $request->stn_national_lang,
                'stn_regional_lang' => $request->stn_regional_lang,
                'cord_x'            => $request->cord_x,
                'cord_y'            => $request->cord_y,
                'start_date'        => $request->start_date,
                'end_date'          => $request->end_date,
                'updated_date'      => now(),
                'updated_by'        => 1,
            ]);

        $this->insertJson($id);

        return redirect()
            ->to('stations')
            ->with([
                'status' => true,
                'message' => $request->stn_name . ' STATION UPDATED SUCCESSFULLY.'
            ]);
    }

    /*STATION DATA IN FORM OF JSON*/
    private function insertJson($stnID)
    {
        $station_data = DB::table('station_inventory')
            ->where('stn_id', '=', $stnID)
            ->first(['stn_inv_id', 'stn_id', 'stn_name', 'stn_national_lang', 'stn_regional_lang', 'stn_short_name', 'description', 'company_id', 'line_id', 'status', 'cord_x', 'cord_y', 'start_date', 'end_date', 'created_date', 'updated_date', 'updated_by']);
        DB::table('station_inventory')
            ->where('stn_id', '=', $stnID)
            ->update([
                'stn_data' => json_encode($station_data)
            ]);
    }

}
