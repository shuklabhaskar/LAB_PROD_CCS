<?php

namespace App\Http\Controllers\Modules\Fare;

use App\Http\Controllers\Controller;
use App\Models\Stations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FareController extends Controller
{
     /*INDEX PAGE DATA*/
    function index(){

        $Fares = DB::table('fare_inventory')
            ->orderBy('fare_table_id','ASC')
            ->get();

        return Inertia::render('Fare/Index', [
            'Fares' => $Fares
        ]);

    }

     /*MASTER DATA*/
    function create(){

        $Stations = DB::table('station_inventory')
            ->join('ms_company_inv', 'ms_company_inv.company_id', '=', 'station_inventory.company_id')
            ->join('ms_line_inventory', 'ms_line_inventory.line_id', '=', 'station_inventory.line_id')
            ->select(['station_inventory.*', 'ms_company_inv.company_name', 'ms_line_inventory.line_name'])
            ->orderBy('station_inventory.stn_id')
            ->get();

        $fare = null;
        for($i = 0; $i < $Stations->count(); $i++) {
            // PRINT COLUMN
            for($j = 0; $j < $Stations->count(); $j++) {
                // PRINT ROW
                $fare[$i][$j] = 0;
            }
        }

        return Inertia::render('Fare/Create', [
            'Stations'  => $Stations,
            'Fare'      => $fare
        ]);
    }

    /*INSERTING FARE DATA*/
    function store(Request $request)
    {
    $newId = DB::table('fare_table')->orderBy('fare_table_id', 'desc')->first('fare_table_id');
    $fareTableID = ($newId != null) ? $newId->fare_table_id + 1 : 1;

       $data = $request->getContent();
       $Destination= json_decode($data);

       $request->validate([
           'fare_name'   => 'required',
           'status'      => 'required|boolean',
           'start_date'  => 'required'
           ]);

        foreach ($Destination->fare as $key => $val) {
            foreach($val as $source => $fare){
                DB::table('fare_table')
                    ->insert([
                        'fare_table_id'  => $fareTableID,
                        'source_id'      => $source + 1,
                        'destination_id' => $key+1,
                        'fare'           => $fare,
                        'fare_version'   => 1,
                    ]);
            }
        }

        DB::table('fare_inventory')->insert([
            'fare_table_id' => $fareTableID,
            'fare_name'     => $Destination->fare_name,
            'description'   => $Destination->description,
            'status'        => $Destination->status,
            'fare_version'  => 1,
            'start_date'    => $Destination->start_date,
            'end_date'      => $Destination->end_date,
            'created_date'  => now(),
        ]);

        $fareData = DB::table('fare_table')->where('fare_table_id', '=', $fareTableID);

                /* INSERTING INTO FARE HISTORY */
                DB::table('fare_table_hist')
                    ->insert([
                        'fare_table_id' => $fareTableID,
                        'fare_data'     => $fareData->get()->toJson(),
                        'fare_version'  => $fareData->get()[0]->fare_version,
                        'start_date'    => now(),
                    ]);

        return redirect('fares')->with(['message' => $Destination->fare_name . ' FARE CREATED SUCCESSFULLY.']);
    }

     /*SHOWING EDITABLE DATA*/
    function edit($id): \Inertia\Response
    {
        $Stations = DB::table('station_inventory')
            ->join('ms_company_inv', 'ms_company_inv.company_id', '=', 'station_inventory.company_id')
            ->join('ms_line_inventory', 'ms_line_inventory.line_id', '=', 'station_inventory.line_id')
            ->select(['station_inventory.*', 'ms_company_inv.company_name', 'ms_line_inventory.line_name'])
            ->orderBy('station_inventory.stn_id')
            ->get();

        $fareInventory = DB::table('fare_inventory')->where('fare_table_id','=',$id)->first();

        $fares = DB::table('fare_inventory')
            ->join('fare_table', 'fare_inventory.fare_table_id', '=', 'fare_table.fare_table_id')
            ->join('station_inventory as a', 'a.stn_id', '=', 'fare_table.source_id')
            ->join('station_inventory as b', 'b.stn_id', '=', 'fare_table.destination_id')
            ->where('fare_table.fare_table_id', $id)
            ->select(['fare_inventory.*', 'a.stn_id', 'a.stn_name', 'fare_table.*'])
            ->orderBy('fare_table.id','ASC')
            ->get();

        $fare = [];
        foreach ($fares as $val) {
            $fare[$val->destination_id][$val->source_id] = $val->fare;
        }

        return Inertia::render('Fare/Edit', [
            'Fare'          => $fare,
            'fareInventory' => $fareInventory,
            'Stations'      => $Stations,
        ]);
    }

    /*UPDATING DATA*/
    function update(Request $request,$id)
    {
        $Validate = $request->validate([
            'fare_name'   => 'required',
            'status'      => 'required|boolean',
            'start_date'  => 'required'
        ]);

        $data = $request->getContent();
        $Destination= json_decode($data);

        $fare_Version = DB::table('fare_table')->where('fare_table_id','=',$id)->select(['fare_version'])->first();

        foreach ($Destination->fare as $key => $val) {
            foreach($val as $source => $fare){
                DB::table('fare_table')
                    ->where('fare_table_id','=',$id)
                    ->where('source_id','=',$source)
                    ->where('destination_id','=',$key)
                    ->update([
                        'fare'           => $fare,
                        'fare_version'   => $fare_Version->fare_version + 1,
                    ]);
            }
        }

        DB::table('fare_inventory')
            ->where('fare_table_id','=',$id)
            ->update([
            'fare_name'     => $Destination->fare_name,
            'description'   => $Destination->description,
            'status'        => $Destination->status,
            'fare_version'  => $fare_Version->fare_version + 1,
            'start_date'    => $Destination->start_date,
            'end_date'      => $Destination->end_date,
            'updated_date'  => now(),
        ]);

        $fareData = DB::table('fare_table')->where('fare_table_id', '=', $id);

        /* INSERTING INTO FARE HISTORY */
        DB::table('fare_table_hist')
            ->insert([
                'fare_table_id' => $id,
                'fare_data'     => $fareData->get()->toJson(),
                'fare_version'  => $fareData->get()[0]->fare_version,
                'start_date'    => now(),
            ]);

        return redirect()
            ->to('fares')
            ->with([
                'status'  => true,
                'message' => $Destination->fare_name. ' FARE UPDATED SUCCESSFULLY.'
            ]);
    }
}
