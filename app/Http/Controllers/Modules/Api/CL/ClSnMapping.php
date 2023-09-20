<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClSnMapping extends Controller
{
    public function index(Request $request){

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
                ->where('chip_id','=',$request->chip_id)
                ->select('engraved_id')
                ->first();


            if ($engravedId == null){
                return response([
                    'status' => false,
                    'message'=> "No EID SN is Available for this Chip Sn "
                ]);
            }

        }

        return response([
            'status' => true,
            'engraved_id'=> $engravedId->engraved_id
        ]);
    }
}
