<?php

namespace App\Http\Controllers\Modules\Api\Firmware;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Firmware extends Controller
{
    /* CHECK UPDATE IS AVAILABLE OR NOT */
    public function checkUpdate(Request $request)
    {
        /* PREDEFINED ARRAY LIST OF EQUIPMENTS */

        $AGEquipments = DB::table('firmware_publish')
            ->where('firmware_id', '=', 1)
            ->where('is_sent', '=', false)
            ->pluck('equipment_id');

        $TOMEquipments = DB::table('firmware_publish')
            ->where('firmware_id', '=', 2)
            ->where('is_sent', '=', false)
            ->pluck('equipment_id');

        $EDCEquipments = DB::table('firmware_publish')
            ->where('firmware_id', '=', 6)
            ->where('is_sent', '=', false)
            ->pluck('equipment_id');

        if (!empty($request->eq_type_id)) {

            /*CHECKING AVAILABLE VERSION FOR AG*/
            if ($request->eq_type_id == 1) {

                if ($AGEquipments->contains($request->input('eq_id'))) {

                    $current_version = DB::table('firmware_publish')
                        ->where('firmware_id', '=', 1)
                        ->where('is_sent', '=', false)
                        ->first('firmware_version');

                    $ID = DB::table('firmware_publish')
                        ->where('equipment_id', '=', $request->input('eq_id'))
                        ->where('is_sent', '=', false)
                        ->first('firmware_upload_id');


                    if ($current_version->firmware_version != null) {
                        if ($current_version->firmware_version != $request->input('current_version')) {
                            return [
                                'status'    => true,
                                'code'      => 100,
                                'uploadID'  => $ID->firmware_upload_id,
                                'message'   => "Yes, New version is available."
                            ];
                        } else {
                            return [
                                'status'    => false,
                                'code'      => 101,
                                'message'   => "You have already latest version of gate application"
                            ];
                        }
                    } else {
                        return [
                            'status'    => false,
                            'code'      => 101,
                            'message'   => "No File is available"
                        ];
                    }


                } else {
                    return [
                        'status'    => false,
                        'code'      => 101,
                        'message'   => "No update is available"
                    ];
                }


            }

            /*CHECKING AVAILABLE VERSION FOR TOM*/
            if ($request->eq_type_id == 2) {
                if ($TOMEquipments->contains($request->input('eq_id'))) {

                    $current_version = DB::table('firmware_publish')
                        ->where('firmware_id', '=', 2)
                        ->where('is_sent', '=', false)
                        ->first('firmware_version');

                    $ID = DB::table('firmware_publish')
                        ->where('equipment_id', '=', $request->input('eq_id'))
                        ->where('is_sent', '=', false)
                        ->first('firmware_upload_id');


                    if ($current_version->firmware_version != null) {

                        if ($current_version->firmware_version != $request->input('current_version')) {
                            return [
                                'status'   => true,
                                'code'     => 100,
                                'uploadID' => $ID->firmware_upload_id,
                                'message'  => "Yes, New version is available."
                            ];
                        } else {
                            return [
                                'status'  => false,
                                'code'    => 101,
                                'message' => "You have already latest version of TOM application"
                            ];
                        }

                    } else {
                        return [
                            'status'  => false,
                            'code'    => 101,
                            'message' => "No File is available"
                        ];
                    }


                } else {
                    return [
                        'status'  => false,
                        'code'    => 101,
                        'message' => "No update is available"
                    ];
                }
            }

            /*CHECKING AVAILABLE VERSION FOR EDC*/
            if ($request->eq_type_id == 6) {

                if ($EDCEquipments->contains($request->input('eq_id'))) {

                    $current_version = DB::table('firmware_publish')
                        ->where('firmware_id', '=', 6)
                        ->where('is_sent', '=', false)
                        ->first('firmware_version');

                    $ID = DB::table('firmware_publish')
                        ->where('equipment_id', '=', $request->input('eq_id'))
                        ->where('is_sent', '=', false)
                        ->first('firmware_upload_id');

                    if ($current_version->firmware_version != null) {
                        if ($current_version->firmware_version != $request->input('eq_id')) {
                            return [
                                'status'    => true,
                                'code'      => 100,
                                'uploadID'  => $ID->firmware_upload_id,
                                'message'   => "Yes, New version is available."
                            ];
                        } else {
                            return [
                                'status'  => false,
                                'code'    => 101,
                                'message' => "You have already latest version of TOM application"
                            ];
                        }
                    }else{
                        return [
                            'status'  => false,
                            'code'    => 101,
                            'message' => "No File is available"
                        ];
                    }

                } else {
                    return [
                        'status'  => false,
                        'code'    => 101,
                        'message' => "No update is available"
                    ];
                }
            }

        }

        return [
            'status'  => false,
            'code'    => 404,
            'message' => "Please provide correct request body"
        ];

    }

    /* DOWNLOADING RESPECTIVE FILE */
    public function getFirmware($uploadId)
    {

        if (!empty($uploadId)) {

            $true = DB::table('firmware_publish')
                ->where('firmware_upload_id','=',$uploadId)
                ->orderBy('is_sent','ASC')
                ->first('is_sent');

            if (!$true->is_sent) {

                $path = DB::table('firmware_upload')
                    ->where('firmware_upload_id','=',$uploadId)
                    ->first('firmware_path');

                DB::table('firmware_publish')
                    ->where('firmware_upload_id','=',$uploadId)
                    ->update([
                        'is_sent'=> true,
                        'updated_at'=>now()
                    ]);

                $downloadPath = storage_path($path->firmware_path);

                return response()->download($downloadPath);

            }else{

                return response([
                    'status' => false,
                    'message' => "No Firmware is Published for TOM"
                ]);

            }

        }

        return response([
            'status' => false,
            'message' => "Check EQ Type ID"
        ]);

    }

}
