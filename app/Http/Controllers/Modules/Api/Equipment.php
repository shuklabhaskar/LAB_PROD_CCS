<?php

namespace App\Http\Controllers\Modules\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Equipment extends Controller
{
    public function eqModeID(Request $request){

        $data = DB::table('ms_equipment_role')
            ->where('eq_type_id','=',$request->eq_type_id)
            ->get();

        return response([
            'status' => true,
            'data'  => $data
        ]);

    }

    public  function checkSCS(Request $request){

         $data = DB::table("equipment_inventory")
            ->where('stn_id','=',$request->stn_id)
            ->where('eq_type_id','=',4)
            ->first();

         if( $data == null){
             return response([
                 'status' => false,
                 'message'  => "No SCS is available"
             ]);
         }else{
             return response([
                 'status' => true,
                 'message'  => "SCS is available"
             ]);
         }


    }

    public  function getRoles(Request $request){

        $data = DB::table('ms_equipment_role')
            ->where('eq_type_id','=',$request->eq_type_id)
            ->get();

        if( $data == null){

            return response([
                'status' => false,
            ]);

        }else{
            return response([
                'status'    => true,
                'data'      => $data
            ]);
        }

    }
}
