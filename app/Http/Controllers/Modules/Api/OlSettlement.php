<?php

namespace App\Http\Controllers\Modules\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OlSettlement extends Controller
{

    public function getSettlements (){

        $settlement = DB::table('ol_settlement')
            ->get();

        if($settlement !== []){

            return response([
                'status'    => true,
                'data'      => $settlement
            ]);

        }

    }

    public function setSettlements (Request $request){

        $settlement = DB::table('ol_settlement')
            ->get();

        if ($settlement == null){

            return response([
                'status'  => true,
                'message' => 'No Data Fetched',
                'data'    => $settlement
            ]);

        }else{

            return response([
                'status'  => true,
                'message' => 'Data Fetched Successfully',
                'data'    => $settlement
            ]);
        }
    }

}
