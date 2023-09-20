<?php

namespace App\Http\Controllers\Modules\Equipment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EquipmentController extends Controller
{
    /*INDEX PAGE DATA*/
    function index()
    {
        $Equipments = DB::table('equipment_inventory as ei')
            ->where('is_blacklisted', '=', 0)
            ->join('station_inventory as si', 'si.stn_id', '=', 'ei.stn_id')
            ->join('ms_equipment_type as et', 'et.eq_type_id', '=', 'ei.eq_type_id')
            ->select('ei.*', 'si.stn_name',  'et.eq_type_id', 'et.eq_type_name')
            ->orderBy('si.stn_id','ASC')
            ->get();

        return Inertia::render('Equipment/Index', [
            'Equipments' => $Equipments
        ]);

    }

    /*MASTER DATA*/
    function create()
    {
        $Stations = DB::table('station_inventory')
            ->join('ms_company_inv', 'ms_company_inv.company_id', '=', 'station_inventory.company_id')
            ->join('ms_line_inventory', 'ms_line_inventory.line_id', '=', 'station_inventory.line_id')
            ->select(['station_inventory.*', 'ms_company_inv.company_name', 'ms_line_inventory.line_name'])
            ->orderBy('stn_inv_id','ASC')
            ->get();

        $EquipmentTypes = DB::table('ms_equipment_type')->get();
        $EquipmentRoles = DB::table('ms_equipment_role')->get();
        $EquipmentModes = DB::table('ms_equipment_mode')->get();
        $EndLocation    = DB::table('ms_equipment_location')->get();

        return Inertia::render('Equipment/Create', [
            'Stations'          => $Stations,
            'EquipmentTypes'    => $EquipmentTypes,
            'EquipmentRoles'    => $EquipmentRoles,
            'EquipmentModes'    => $EquipmentModes,
            'EndLocation'       => $EndLocation,
        ]);
    }

    /*INSERTING EQUIPMENT DATA*/
    function store(Request $request)
    {
        $request->validate([
            'stn_id'            => 'required|integer',
            'eq_id'             => 'required|unique:equipment_inventory',
            'eq_type_id'        => 'required|integer',
            'status'            => 'required|boolean',
            'start_date'        => 'required',
            'ip_address'        => 'required|ipv4|unique:equipment_inventory',
            'primary_ssid'      => 'required',
            'primary_ssid_pwd'  => 'required',
            'gateway'           => 'required',
            'subnetmask'        => 'required',
            'eq_num'            => 'required',
        ]);

        $newId = DB::table('equipment_inventory')->orderBy('eq_inv_id', 'desc')->first('eq_inv_id');
        $eqID = ($newId != null) ? $newId->eq_inv_id + 1 : 1;

        /*FOR AG ENTRY*/
        if ($request->input('eq_mode_id') == 1){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_type_id'        => 'required|integer',
                'eq_mode_id'        => 'required|integer',
                'eq_role_id'        => 'required|integer',
                'eq_loc_id'         => 'required|integer',
                'stn_id'            => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required',
            ]);

            DB::table('equipment_inventory')->insert([
                'atek_eq_id'        => $request->input('atek_eq_id'),
                'eq_type_id'        => $request->input('eq_type_id'),
                'eq_mode_id'        => $request->input('eq_mode_id'),
                'eq_role_id'        => $request->input('eq_role_id'),
                'eq_num'            => $request->input('eq_num'),
                'eq_id'             => $request->input('eq_id'),
                'eq_location_id'    => $request->input('eq_loc_id'),
                'stn_id'            => $request->input('stn_id'),
                'description'       => $request->input('description'),
                'cord_x'            => $request->input('cord_x'),
                'cord_y'            => $request->input('cord_y'),
                'status'            => $request->input('status'),
                'start_date'        => $request->input('start_date'),
                'end_date'          => $request->input('end_date'),
                'ip_address'        => $request->input('ip_address'),
                'primary_ssid'      => $request->input('primary_ssid'),
                'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                'backup_ssid'       => $request->input('backup_ssid'),
                'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                'gateway'           => $request->input('gateway'),
                'subnetmask'        => $request->input('subnetmask'),
                'eq_version'        => 1,
                'created_date'      => now(),
            ]);

        }

        /*FOR AG EXIT*/
        if ($request->input('eq_mode_id') == 2){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_type_id'        => 'required|integer',
                'eq_mode_id'        => 'required|integer',
                'eq_role_id'        => 'required|integer',
                'eq_num'            => 'required',
                'eq_loc_id'         => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required',
            ]);

            DB::table('equipment_inventory')->insert([
                'atek_eq_id'        => $request->input('atek_eq_id'),
                'eq_type_id'        => $request->input('eq_type_id'),
                'eq_mode_id'        => $request->input('eq_mode_id'),
                'eq_role_id'        => $request->input('eq_role_id'),
                'eq_num'            => $request->input('eq_num'),
                'eq_id'             => $request->input('eq_id'),
                'eq_location_id'    => $request->input('eq_loc_id'),
                'stn_id'            => $request->input('stn_id'),
                'description'       => $request->input('description'),
                'cord_x'            => $request->input('cord_x'),
                'cord_y'            => $request->input('cord_y'),
                'status'            => $request->input('status'),
                'start_date'        => $request->input('start_date'),
                'end_date'          => $request->input('end_date'),
                'ip_address'        => $request->input('ip_address'),
                'primary_ssid'      => $request->input('primary_ssid'),
                'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                'backup_ssid'       => $request->input('backup_ssid'),
                'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                'gateway'           => $request->input('gateway'),
                'subnetmask'        => $request->input('subnetmask'),
                'eq_version'        => 1,
                'created_date'      => now(),
            ]);

        }

        /*FOR AG BIDI*/
        if ($request->input('eq_mode_id') == 3){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_mode_id'        => 'required|integer',
                'eq_role_id'        => 'required|integer',
                'eq_loc_id'         => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required',
            ]);

            DB::table('equipment_inventory')->insert([
                'atek_eq_id'        => $request->input('atek_eq_id'),
                'eq_type_id'        => $request->input('eq_type_id'),
                'eq_mode_id'        => $request->input('eq_mode_id'),
                'eq_role_id'        => $request->input('eq_role_id'),
                'eq_num'            => $request->input('eq_num'),
                'eq_id'             => $request->input('eq_id'),
                'eq_location_id'    => $request->input('eq_loc_id'),
                'stn_id'            => $request->input('stn_id'),
                'description'       => $request->input('description'),
                'cord_x'            => $request->input('cord_x'),
                'cord_y'            => $request->input('cord_y'),
                'status'            => $request->input('status'),
                'start_date'        => $request->input('start_date'),
                'end_date'          => $request->input('end_date'),
                'ip_address'        => $request->input('ip_address'),
                'primary_ssid'      => $request->input('primary_ssid'),
                'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                'backup_ssid'       => $request->input('backup_ssid'),
                'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                'gateway'           => $request->input('gateway'),
                'subnetmask'        => $request->input('subnetmask'),
                'eq_version'        => 1,
                'created_date'      => now(),
            ]);

        }

        /* FOR SCS */
        if ($request->input('eq_type_id') ==  4 ){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_num'            => 'required',
                'eq_loc_id'         => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required|integer',
            ]);

            DB::table('equipment_inventory')->insert([
                'atek_eq_id'        => $request->input('atek_eq_id'),
                'eq_type_id'        => $request->input('eq_type_id'),
                'eq_num'            => $request->input('eq_num'),
                'eq_id'             => $request->input('eq_id'),
                'eq_location_id'    => $request->input('eq_loc_id'),
                'stn_id'            => $request->input('stn_id'),
                'description'       => $request->input('description'),
                'cord_x'            => $request->input('cord_x'),
                'cord_y'            => $request->input('cord_y'),
                'status'            => $request->input('status'),
                'start_date'        => $request->input('start_date'),
                'end_date'          => $request->input('end_date'),
                'ip_address'        => $request->input('ip_address'),
                'primary_ssid'      => $request->input('primary_ssid'),
                'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                'backup_ssid'       => $request->input('backup_ssid'),
                'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                'gateway'           => $request->input('gateway'),
                'subnetmask'        => $request->input('subnetmask'),
                'eq_version'        => 1,
                'created_date'      => Carbon::now(),
            ]);

        }

        /* FOR TOM */
        if ($request->input('eq_type_id') == 2){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_num'            => 'required',
                'eq_loc_id'         => 'required|integer',
                'eq_role_id'        => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required',
            ]);

            DB::table('equipment_inventory')->insert([
                'atek_eq_id'        => $request->input('atek_eq_id'),
                'eq_type_id'        => $request->input('eq_type_id'),
                'eq_mode_id'        => $request->input('eq_mode_id'),
                'eq_role_id'        => $request->input('eq_role_id'),
                'eq_num'            => $request->input('eq_num'),
                'eq_id'             => $request->input('eq_id'),
                'eq_location_id'    => $request->input('eq_loc_id'),
                'stn_id'            => $request->input('stn_id'),
                'description'       => $request->input('description'),
                'cord_x'            => $request->input('cord_x'),
                'cord_y'            => $request->input('cord_y'),
                'status'            => $request->input('status'),
                'start_date'        => $request->input('start_date'),
                'end_date'          => $request->input('end_date'),
                'ip_address'        => $request->input('ip_address'),
                'primary_ssid'      => $request->input('primary_ssid'),
                'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                'backup_ssid'       => $request->input('backup_ssid'),
                'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                'gateway'           => $request->input('gateway'),
                'subnetmask'        => $request->input('subnetmask'),
                'eq_version'        => 1,
                'created_date'      => now(),

            ]);

        }

        $this->insertJson($eqID);

        return redirect('equipments')->with([
            'status' => true,
            'message' => $request->input('eq_id') . ' EQUIPMENT CREATED SUCCESSFULLY.'
        ]);

    }

    /*EDIT VIEW FOR EQUIPMENTS*/
    function  edit($id)
    {
        $Stations = DB::table('station_inventory')
            ->join('ms_company_inv', 'ms_company_inv.company_id', '=', 'station_inventory.company_id')
            ->join('ms_line_inventory', 'ms_line_inventory.line_id', '=', 'station_inventory.line_id')
            ->select(['station_inventory.*', 'ms_company_inv.company_name', 'ms_line_inventory.line_name'])
            ->orderBy('stn_inv_id','ASC')
            ->get();

        $EquipmentTypes = DB::table('ms_equipment_type')->get();
        $EquipmentRoles = DB::table('ms_equipment_role')->get();
        $EquipmentModes = DB::table('ms_equipment_mode')->get();
        $EndLocation    = DB::table('ms_equipment_location')->get();

        $Equipment = DB::table('equipment_inventory as ei')
            ->where('ei.eq_inv_id', '=', $id)
            ->where('is_blacklisted', '=', 0)
            ->join('station_inventory as si', 'si.stn_id', '=', 'ei.stn_id')
            ->join('ms_equipment_type as et', 'et.eq_type_id', '=', 'ei.eq_type_id')
            ->select('ei.*', 'si.stn_name',  'et.eq_type_id', 'et.eq_type_name' )
            ->first();

        return Inertia::render('Equipment/Edit', [
            'Stations'          => $Stations,
            'EquipmentTypes'    => $EquipmentTypes,
            'EquipmentRoles'    => $EquipmentRoles,
            'EquipmentModes'    => $EquipmentModes,
            'Equipment'         => $Equipment,
            'EndLocation'       => $EndLocation
        ]);

    }

    /* UPDATE */
    function update(Request $request, $id)
    {
        $request->validate([
            'stn_id'            => 'required|integer',
            'eq_type_id'        => 'required|integer',
            'status'            => 'required|boolean',
            'start_date'        => 'required',
            'ip_address'        => 'required|ipv4',
            'primary_ssid'      => 'required',
            'primary_ssid_pwd'  => 'required',
            'gateway'           => 'required',
            'subnetmask'        => 'required',
        ]);

        $data = DB::table('equipment_inventory')->orderBy('eq_inv_id', 'desc')->first('eq_version');
        $eq_version = ($data != null) ? $data->eq_version + 1 : 1;

        /* FOR ENTRY GATE */
        if ($request->input('eq_mode_id') == 1){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_type_id'        => 'required|integer',
                'eq_mode_id'        => 'required|integer',
                'eq_role_id'        => 'required|integer',
                'eq_num'            => 'required',
                'eq_loc_id'         => 'required|integer',
                'stn_id'            => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required',
            ]);

            DB::table('equipment_inventory')
                ->where('eq_inv_id', '=', $id)
                ->update([
                    'atek_eq_id'        => $request->input('atek_eq_id'),
                    'eq_type_id'        => $request->input('eq_type_id'),
                    'eq_mode_id'        => $request->input('eq_mode_id'),
                    'eq_role_id'        => $request->input('eq_role_id'),
                    'eq_num'            => $request->input('eq_num'),
                    'stn_id'            => $request->input('stn_id'),
                    'eq_id'             => $request->input('eq_id'),
                    'eq_location_id'    => $request->input('eq_loc_id'),
                    'description'       => $request->input('description'),
                    'cord_x'            => $request->input('cord_x'),
                    'cord_y'            => $request->input('cord_y'),
                    'status'            => $request->input('status'),
                    'start_date'        => $request->input('start_date'),
                    'end_date'          => $request->input('end_date'),
                    'ip_address'        => $request->input('ip_address'),
                    'primary_ssid'      => $request->input('primary_ssid'),
                    'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                    'backup_ssid'       => $request->input('backup_ssid'),
                    'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                    'gateway'           => $request->input('gateway'),
                    'subnetmask'        => $request->input('subnetmask'),
                    'eq_version'        => $eq_version,
                    'updated_date'      => now(),
                    'updated_by'        => 1,
                ]);

        }

        /* FOR EXIT GATE */
        if ($request->input('eq_mode_id') == 2){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_type_id'        => 'required|integer',
                'eq_mode_id'        => 'required|integer',
                'eq_role_id'        => 'required|integer',
                'eq_num'            => 'required',
                'eq_loc_id'         => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required',

            ]);

            DB::table('equipment_inventory')
                ->where('eq_inv_id', '=', $id)
                ->update([
                    'atek_eq_id'        => $request->input('atek_eq_id'),
                    'eq_type_id'        => $request->input('eq_type_id'),
                    'eq_mode_id'        => $request->input('eq_mode_id'),
                    'eq_role_id'        => $request->input('eq_role_id'),
                    'eq_num'            => $request->input('eq_num'),
                    'stn_id'            => $request->input('stn_id'),
                    'eq_location_id'    => $request->input('eq_loc_id'),
                    'description'       => $request->input('description'),
                    'cord_x'            => $request->input('cord_x'),
                    'cord_y'            => $request->input('cord_y'),
                    'status'            => $request->input('status'),
                    'start_date'        => $request->input('start_date'),
                    'end_date'          => $request->input('end_date'),
                    'ip_address'        => $request->input('ip_address'),
                    'primary_ssid'      => $request->input('primary_ssid'),
                    'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                    'backup_ssid'       => $request->input('backup_ssid'),
                    'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                    'gateway'           => $request->input('gateway'),
                    'subnetmask'        => $request->input('subnetmask'),
                    'eq_version'        => $eq_version,
                    'updated_date'      => now(),
                    'updated_by'        => 1,
                ]);
        }

        /* FOR BIDI GATE */
        if ($request->input('eq_mode_id') == 3){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_mode_id'        => 'required|integer',
                'eq_role_id'        => 'required|integer',
                'eq_num'            => 'required',
                'eq_loc_id'         => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required',
            ]);

            DB::table('equipment_inventory')
                ->where('eq_inv_id', '=', $id)
                ->update([
                    'atek_eq_id'        => $request->input('atek_eq_id'),
                    'eq_type_id'        => $request->input('eq_type_id'),
                    'eq_mode_id'        => $request->input('eq_mode_id'),
                    'eq_role_id'        => $request->input('eq_role_id'),
                    'eq_num'            => $request->input('eq_num'),
                    'stn_id'            => $request->input('stn_id'),
                    'eq_id'             => $request->input('eq_id'),
                    'eq_location_id'    => $request->input('eq_loc_id'),
                    'description'       => $request->input('description'),
                    'cord_x'            => $request->input('cord_x'),
                    'cord_y'            => $request->input('cord_y'),
                    'status'            => $request->input('status'),
                    'start_date'        => $request->input('start_date'),
                    'end_date'          => $request->input('end_date'),
                    'ip_address'        => $request->input('ip_address'),
                    'primary_ssid'      => $request->input('primary_ssid'),
                    'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                    'backup_ssid'       => $request->input('backup_ssid'),
                    'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                    'gateway'           => $request->input('gateway'),
                    'subnetmask'        => $request->input('subnetmask'),
                    'eq_version'        => $eq_version,
                    'updated_date'      => now(),
                    'updated_by'        => 1,
                ]);

        }

        /* FOR SCS */
        if ($request->input('eq_type_id') == 4){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_num'            => 'required',
                'eq_loc_id'         => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required|integer',

            ]);

            DB::table('equipment_inventory')
                ->where('eq_inv_id', '=', $id)
                ->update([
                    'atek_eq_id'        => $request->input('atek_eq_id'),
                    'eq_type_id'        => $request->input('eq_type_id'),
                    'eq_num'            => $request->input('eq_num'),
                    'stn_id'            => $request->input('stn_id'),
                    'eq_id'             => $request->input('eq_id'),
                    'eq_location_id'    => $request->input('eq_loc_id'),
                    'description'       => $request->input('description'),
                    'cord_x'            => $request->input('cord_x'),
                    'cord_y'            => $request->input('cord_y'),
                    'status'            => $request->input('status'),
                    'start_date'        => $request->input('start_date'),
                    'end_date'          => $request->input('end_date'),
                    'ip_address'        => $request->input('ip_address'),
                    'primary_ssid'      => $request->input('primary_ssid'),
                    'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                    'backup_ssid'       => $request->input('backup_ssid'),
                    'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                    'gateway'           => $request->input('gateway'),
                    'subnetmask'        => $request->input('subnetmask'),
                    'eq_version'        => $eq_version,
                    'updated_date'      => now(),
                    'updated_by'        => 1,
                ]);

        }

        /* FOR TOM */
        if ($request->input('eq_type_id') == 2){

            $request->validate([
                'atek_eq_id'        => 'required',
                'eq_num'            => 'required',
                'eq_loc_id'         => 'required|integer',
                'eq_role_id'        => 'required|integer',
                'cord_x'            => 'required',
                'cord_y'            => 'required',

            ]);

            DB::table('equipment_inventory')
                ->where('eq_inv_id', '=', $id)
                ->update([
                    'atek_eq_id'        => $request->input('atek_eq_id'),
                    'eq_type_id'        => $request->input('eq_type_id'),
                    'eq_mode_id'        => $request->input('eq_mode_id'),
                    'eq_role_id'        => $request->input('eq_role_id'),
                    'eq_num'            => $request->input('eq_num'),
                    'stn_id'            => $request->input('stn_id'),
                    'eq_id'             => $request->input('eq_id'),
                    'eq_location_id'    => $request->input('eq_loc_id'),
                    'description'       => $request->input('description'),
                    'cord_x'            => $request->input('cord_x'),
                    'cord_y'            => $request->input('cord_y'),
                    'status'            => $request->input('status'),
                    'start_date'        => $request->input('start_date'),
                    'end_date'          => $request->input('end_date'),
                    'ip_address'        => $request->input('ip_address'),
                    'primary_ssid'      => $request->input('primary_ssid'),
                    'primary_ssid_pwd'  => $request->input('primary_ssid_pwd'),
                    'backup_ssid'       => $request->input('backup_ssid'),
                    'backup_ssid_pwd'   => $request->input('backup_ssid_pwd'),
                    'gateway'           => $request->input('gateway'),
                    'subnetmask'        => $request->input('subnetmask'),
                    'eq_version'        => $eq_version,
                    'updated_date'      => now(),
                    'updated_by'        => 1,
                ]);
        }

        $this->insertJson($id);

        return redirect('equipments')->with([
            'status' => true,
            'message' => $request->input('eq_id') .' EQUIPMENT UPDATED SUCCESSFULLY.'
        ]);

    }

    /*EQ DATA IN FORM OF JSON*/
    private function insertJson($eqID)
    {
        $eqData = DB::table('equipment_inventory')
            ->where('eq_inv_id', '=', $eqID)
            ->first(['eq_version','eq_inv_id','atek_eq_id','eq_type_id','eq_mode_id','eq_role_id','eq_num','eq_id','stn_id','eq_location_id','description','cord_x','cord_y','status','start_date','end_date','ip_address','primary_ssid','primary_ssid_pwd','backup_ssid','backup_ssid_pwd','gateway','subnetmask','created_date','updated_date','updated_by']);

        DB::table('equipment_inventory')
            ->where('eq_inv_id', '=', $eqID)
            ->update([
                'eq_data' => json_encode($eqData)
            ]);

    }
}
