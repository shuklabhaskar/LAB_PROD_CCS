<?php

namespace App\Http\Controllers\Modules\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class UserRole extends Controller
{
    public function index(){

        $userRoles = DB::table('ms_user_role')->orderBy('user_role_id','ASC')->get();
        return Inertia::render('UserRole/Index',[
            'userRoles'=>$userRoles
        ]);
    }

    public function create(){
        return Inertia::render('UserRole/Create',[

        ]);
    }

    public function store(Request $request){

        $data = $request->validate([
            'user_role_name'=>'required'
        ]);

        DB::table('ms_user_role')->insert([
            'user_role_name' => $request->user_role_name,
            'description'    => $request->description,
            'created_by'     => 1,
        ]);

        return redirect()
            ->to('userRole')
            ->with([
                'status'    => true,
                'message'   => ' USER ROLE CREATED SUCCESSFULLY.'
            ]);
    }

    public function edit($id){

        $userRole = DB::table('ms_user_role')->where('user_role_id','=',$id)->first();

        return Inertia::render('UserRole/Edit',[
            'userRole' => $userRole
        ]);
    }

    public function update(Request $request,$userID){

        $data = $request->validate([
            'user_role_name'=>'required'
        ]);

        DB::table('ms_user_role')->where('user_role_id','=',$userID)->update([
            'user_role_name' => $request->user_role_name,
            'description'    => $request->description,
            'updated_date'     => Carbon::now(),
            'updated_by'     => 1
        ]);

        return redirect()
            ->to('userRole')
            ->with([
                'status' => true,
                'message' => ' USER ROLE UPDATED SUCCESSFULLY.'
            ]);
    }
}
