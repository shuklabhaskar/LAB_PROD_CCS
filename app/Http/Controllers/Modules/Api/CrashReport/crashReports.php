<?php

namespace App\Http\Controllers\Modules\Api\CrashReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class crashReports extends Controller
{
    public function gateLog(Request $request){

        DB::table('log_gate')->insert([
           'log'=>$request->getContent(),
        ]);

        return response([
            'status' => true,
            'message' => "log stored successfully"
        ]);
    }

    public function tomLog(Request $request){

        DB::table('log_tom')->insert([
            'log'=>$request->getContent(),
        ]);

        return response([
            'status' => true,
            'message' => "log stored successfully"
        ]);
    }

    public function edcLog(Request $request){
        DB::table('log_edc')->insert([
            'log'=>$request->getContent(),
        ]);

        return response([
            'status' => true,
            'message' => "log stored successfully"
        ]);
    }
}
