<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClSnMapping extends Controller
{
    public function index(Request $request)
    {

        /* VALIDATION */
        $validator = Validator::make($request->all(), [
            'chip_id' => 'required'
        ]);

        /* IF VALIDATION FAILS */
        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'error' => $validator->errors()
            ]);

        } else {

            $engravedId = DB::table('cl_sn_mapping')
                ->where('chip_id', '=', $request->chip_id)
                ->select('engraved_id')
                ->first();


            if ($engravedId == null) {
                return response([
                    'status' => false,
                    'message' => "No EID Found !"
                ]);
            }

        }

        return response([
            'status' => true,
            'engraved_id' => $engravedId->engraved_id
        ]);
    }

    public function checkEngravedId(Request $request)
    {
        $engravedIds = $request->input('engraved_ids');
        $notFoundEngravedIds = [];

        foreach ($engravedIds as $engravedId) {
            $existingRecord = DB::table('cl_sn_mapping')
                ->where('engraved_id', $engravedId)
                ->first();

            if (is_null($existingRecord)) {
                $notFoundEngravedIds[] = $engravedId;
            }
        }

        $response = [
            'status' => true,
            'engraved_ids' => $notFoundEngravedIds,
        ];

        return response()->json($response);
    }


}
