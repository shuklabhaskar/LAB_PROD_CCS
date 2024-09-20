<?php

namespace App\Http\Controllers\Modules\Api\OperatorPrivilege;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OperatorPrivilegeApi extends Controller
{
    public function index()
    {
        $data = DB::table('operators_api_privilege')->get();

        if ($data == null) {
            return response([
                'status' => false,
                'code'   => 102,
                'error'  => 'No Api Privilege is available!'
            ]);
        }

        return response([
            'status' => false,
            'code'   => 102,
            'data'   => $data
        ]);

    }
}
