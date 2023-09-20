<?php

namespace App\Http\Controllers\Modules\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TidController extends Controller
{
    public function tidDetails(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $data = DB::table('tid_inv')
            ->where('emv_box_serial_no', '=', $request->input('emv_box_serial_number'))
            ->orderBy('tid_inv_id')
            ->first();

        if ($data == "" || $data == null) {
            return response([
                    'status' => true,
                    'message' => "No TID available for this Serial No"
                ]
            );
        } else {

            return response([
                    'status' => true,
                    'data' => $data,
                ]
            );
        }
    }

    public function entryTidDetails(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        $data = DB::table('tid_inv')
            ->where('eq_id', '=', $request->input('eq_id'))
            ->where('eq_dir_id', '=', 1)
            ->first();

        return response([
                'status' => true,
                'data' => $data,
            ]
        );
    }

    public function exitTidDetails(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        $data = DB::table('tid_inv')
            ->where('eq_id', '=', $request->input('eq_id'))
            ->where('eq_dir_id', '=', 2)
            ->first();

        return response([
                'status' => true,
                'data' => $data,
            ]
        );
    }

    public function editEdcDetails(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        $data = DB::table('tid_inv')
            ->where('eq_id', '=', $request->input('eq_id'))
            ->where('eq_dir_id', '=', null)
            ->first();

        return response([
                'status' => true,
                'data' => $data,
            ]
        );
    }

}
