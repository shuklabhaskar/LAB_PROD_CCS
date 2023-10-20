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
        /**
         * {
         * "eq_type_id": 1,
         * "current_version": 0,
         * "eq_id": "120182"
         * }
         */

        $update = DB::table('firmware_publish')
            ->where('eq_type_id', '=', $request->input('eq_type_id'))
            ->where('equipment_id', '=', $request->input('eq_id'))
            ->where('firmware_version', '!=', $request->input('current_version'))
            ->where('is_sent', '=', false)
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($update == null) {
            return response([
                'status' => false,
                'code' => 101,
                'message' => "No update is available"
            ]);
        }

        return response([
            'status' => true,
            'code' => 100,
            'uploadID' => $update->firmware_publish_id,
            'message' => "Yes, New version is available."
        ]);

    }

    /* DOWNLOADING RESPECTIVE FILE */
    public function getFirmware($uploadId)
    {
        if (empty($uploadId)) {
            return response([
                'status' => false,
                'message' => "Check EQ Type ID"
            ]);
        }

        $publish = DB::table('firmware_publish')
            ->where('firmware_publish_id', '=', $uploadId)
            ->where('is_sent', '=', false)
            ->first();

        if ($publish == null) {
            return response([
                'status' => false,
                'message' => "Invalid publish id !"
            ]);
        }

        $firmware = DB::table('firmware_upload')
            ->where('firmware_upload_id', '=', $publish->firmware_upload_id)
            ->first();

        if ($firmware == null) {
            return response([
                'status' => false,
                'message' => "No update is available !"
            ]);
        }

        DB::table('firmware_publish')
            ->where('firmware_publish_id', '=', $uploadId)
            ->update([
                'is_sent' => true,
                'updated_at' => now()
            ]);

        $downloadPath = storage_path($firmware->firmware_path);
        return response()->download($downloadPath);

    }

}
