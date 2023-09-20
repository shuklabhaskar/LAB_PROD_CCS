<?php

namespace App\Http\Controllers\Modules\Firmware;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FirmwarePublish extends Controller
{
    /* SHOWING ALL VERSION FOR RESPECTIVE FIRMWARE */
    public function index()
    {

        $AGEquipments = DB::table('equipment_inventory as ei')
            ->where('is_blacklisted', '=', 0)
            ->where('ei.status','=',true)
            ->where('ei.eq_type_id','=',1)
            ->join('station_inventory as si', 'si.stn_id', '=', 'ei.stn_id')
            ->join('ms_equipment_type as et', 'et.eq_type_id', '=', 'ei.eq_type_id')
            ->select('ei.*', 'si.stn_name', 'et.eq_type_id', 'et.eq_type_name')
            ->orderBy('si.stn_id','ASC')
            ->get();

        $TOMEquipments = DB::table('equipment_inventory as ei')
            ->where('is_blacklisted', '=', 0)
            ->where('ei.status','=',true)
            ->where('ei.eq_type_id','=',2)
            ->join('station_inventory as si', 'si.stn_id', '=', 'ei.stn_id')
            ->join('ms_equipment_type as et', 'et.eq_type_id', '=', 'ei.eq_type_id')
            ->select('ei.*', 'si.stn_name', 'et.eq_type_id', 'et.eq_type_name')
            ->orderBy('si.stn_id','ASC')
            ->get();

        $EDCEquipments = DB::table('equipment_inventory as ei')
            ->where('is_blacklisted', '=', 0)
            ->where('ei.status','=',true)
            ->where('ei.eq_type_id','=',2)
            ->join('station_inventory as si', 'si.stn_id', '=', 'ei.stn_id')
            ->join('ms_equipment_type as et', 'et.eq_type_id', '=', 'ei.eq_type_id')
            ->select('ei.*', 'si.stn_name', 'et.eq_type_id', 'et.eq_type_name')
            ->orderBy('si.stn_id','ASC')
            ->get();

        $SCSEquipments = DB::table('equipment_inventory as ei')
            ->where('is_blacklisted', '=', 0)
            ->where('ei.status','=',true)
            ->where('ei.eq_type_id','=',4)
            ->join('station_inventory as si', 'si.stn_id', '=', 'ei.stn_id')
            ->join('ms_equipment_type as et', 'et.eq_type_id', '=', 'ei.eq_type_id')
            ->select('ei.*', 'si.stn_name', 'et.eq_type_id', 'et.eq_type_name')
            ->orderBy('si.stn_id','ASC')
            ->get();

        $AGVersions = DB::table('firmware_upload')
            ->where('firmware_id','=',1)
            ->distinct('firmware_version')
            ->get('firmware_version');

        $TOMVersions = DB::table('firmware_upload')
            ->where('firmware_id','=',2)
            ->distinct('firmware_version')
            ->get('firmware_version');

        $EDCVersions = DB::table('firmware_upload')
            ->where('firmware_id','=',6)
            ->distinct('firmware_version')
            ->get('firmware_version');

        return Inertia::render('Firmware/FirmwarePublish', [
            'AGEquipments'  => $AGEquipments,
            'TOMEquipments' => $TOMEquipments,
            'EDCEquipments' => $EDCEquipments,
            'SCSEquipments' => $SCSEquipments,
            'AGVersions'    => $AGVersions,
            'TOMVersions'   => $TOMVersions,
            'EDCVersions'   => $EDCVersions,
        ]);
    }

    /* STORING SELECTED DATA IN DATA TABLE*/
    public function store(Request $request)
    {

        $data = json_decode($request->getContent());

        /* FOR AG */
        if ($data->TOM_selected == [] && $data->EDC_selected == [] && $data->SCS_selected == []){

            /*VALIDATIONS*/
            /*$request->validate(
                [
                    'AGVersion' => 'required',
                ],
                [
                    'AGVersion.required'    => 'AG version field cannot be empty.',
                ]
            );*/

            foreach ($data->AG_selected as $key => $eq_id) {

                $ID = DB::table('firmware_upload')
                    ->where('firmware_version','=',$data->AG->AGVersion)
                    ->where('firmware_id','=',1)
                    ->orderBy('firmware_upload_id','DESC')
                    ->first('firmware_upload_id');

                DB::table('firmware_publish')->insert([
                    'equipment_id'       =>$eq_id,
                    'firmware_id'        =>1,
                    'firmware_upload_id' =>$ID->firmware_upload_id,
                    'firmware_version'   =>$data->AG->AGVersion,
                    'description'        =>$data->AG->AG_description,
                    'sent_by'            =>1,
                    'is_published'       =>1,
                    'activation_time'    =>now(),
                ]);
            }
        }

        /* FOR TOM */
        if ($data->AG_selected == [] && $data->EDC_selected == [] && $data->SCS_selected == []) {

            /*VALIDATIONS*/
            /*$request->validate(
                [
                    'TOMVersion' => 'required',
                ],
                [
                    'TOMVersion.required' => 'TOM version field cannot be empty.'
                ]
            );*/

            foreach ($data->TOM_selected as $key => $eq_id) {

                $ID = DB::table('firmware_upload')
                    ->where('firmware_version','=',$data->TOM->TOMVersion)
                    ->where('firmware_id','=',2)
                    ->orderBy('firmware_upload_id','DESC')
                    ->first('firmware_upload_id');

                DB::table('firmware_publish')->insert([
                    'equipment_id'       => $eq_id,
                    'firmware_id'        => 2,
                    'firmware_upload_id' => $ID->firmware_upload_id,
                    'firmware_version'   => $data->TOM->TOMVersion,
                    'description'        => $data->TOM->TOM_description,
                    'sent_by'            => 1,
                    'is_published'       => 1,
                    'activation_time'    => now(),
                ]);
            }

        }

        /* FOR EDC */
        if ($data->AG_selected == [] && $data->TOM_selected == [] && $data->SCS_selected == []) {

            /*VALIDATIONS*/
           /* $request->validate(
                [
                    'EDCVersion' => 'required',
                ],
                [
                    'EDCVersion.required' => 'EDC version field cannot be empty.',
                ]
            );*/

            foreach ($data->EDC_selected as $key => $eq_id) {

                $ID = DB::table('firmware_upload')
                    ->where('firmware_version','=',$data->EDC->EDCVersion)
                    ->where('firmware_id','=',6)
                    ->orderBy('firmware_upload_id','DESC')
                    ->first('firmware_upload_id');

                DB::table('firmware_publish')->insert([
                    'equipment_id'       => $eq_id,
                    'firmware_id'        => 6,
                    'firmware_upload_id' => $ID->firmware_upload_id,
                    'firmware_version'   => $data->EDC->EDCVersion,
                    'description'        => $data->EDC->EDC_description,
                    'sent_by'            => 1,
                    'is_published'       => 1,
                    'activation_time'    => now(),
                ]);

            }

        }

        /* FOR SCS */
        if ($data->AG_selected == [] && $data->TOM_selected == [] && $data->EDC_selected == []) {

            /*VALIDATIONS*/
            /*$request->validate(
                [
                    'SCSVersion' => 'required',
                ],
                [
                    'SCSVersion.required' => 'SCS version field cannot be empty.',
                ]
            );*/

            foreach ($data->SCS_selected as $key => $eq_id) {

                $ID = DB::table('firmware_upload')
                    ->where('firmware_version','=',$data->SCS->SCSVersion)
                    ->where('firmware_id','=',4)
                    ->orderBy('firmware_upload_id','DESC')
                    ->first('firmware_upload_id');

                DB::table('firmware_publish')->insert([
                    'equipment_id'       => $eq_id,
                    'firmware_id'        => 4,
                    'firmware_upload_id' => $ID->firmware_upload_id,
                    'firmware_version'   => $data->SCS->SCSVersion,
                    'description'        => $data->SCS->SCS_description,
                    'sent_by'            => 1,
                    'is_published'       => 1,
                    'activation_time'    => now(),
                ]);
            }
        }

        return redirect()
            ->to('firmwarePublish')
            ->with([
                'status'  => true,
                'message' => 'FIRMWARE PUBLISHED SUCCESSFULLY.'
            ]);

    }

}
