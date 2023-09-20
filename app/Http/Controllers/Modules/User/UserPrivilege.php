<?php

namespace App\Http\Controllers\Modules\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserPrivilege extends Controller
{
    public function index(){

        $userPrivileges = DB::table('ms_user_privilege')->orderBy('user_pri_id','ASC')->get();

        return Inertia::render('UserPrivilege/Index',[
            'userPrivileges' => $userPrivileges
        ]);
    }


    public function create(){
        return Inertia::render('UserPrivilege/Create',[

        ]);
    }

    public function store(Request $request){

        $data = $request->validate([
            'user_pri_name'=>'required'
        ]);

        DB::table('ms_user_privilege')->insert([
            'user_pri_name'  => $request->user_pri_name,
            'description'    => $request->description,
            'created_by'     => 1,
        ]);

        return redirect()
            ->to('userPrivilege')
            ->with([
                'status'    => true,
                'message'   => ' USER PRIVILEGE CREATED SUCCESSFULLY.'
            ]);
    }

    public function edit($id){

        $userPrivilege = DB::table('ms_user_privilege')->where('user_pri_id','=',$id)->first();

        return Inertia::render('UserPrivilege/Edit',[
            'userPrivilege' => $userPrivilege
        ]);
    }

    public function update(Request $request,$id){


        DB::table('ms_user_privilege')->where('user_pri_id','=',$id)->update([
            'user_pri_name'     => $request->user_pri_name,
            'description'       => $request->description,
            'updated_date'      => Carbon::now(),
            'updated_by'        => 1
        ]);

        return redirect()
            ->to('userPrivilege')
            ->with([
                'status' => true,
                'message' => ' USER PRIVILEGE UPDATED SUCCESSFULLY.'
            ]);
    }
}
