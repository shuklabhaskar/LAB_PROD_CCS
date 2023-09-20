<?php

namespace App\Http\Controllers\Modules\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;


class UserController extends Controller
{
    public function index(): Response
    {
        $Users = DB::table('user_inventory as ui')
            ->join('ms_user_role as ur','ur.user_role_id','=','ui.user_role_id')
            ->orderBy('user_id','ASC')
            ->get();

        return Inertia::render('User/Index',[
            'Users' => $Users
        ]);
    }

    public function create(): Response
    {

        $userRoles = DB::table('ms_user_role')->get();

        return Inertia::render('User/Create',[
            'userRoles' => $userRoles
        ]);

    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'    => 'required',
            'last_name'     => 'required',
            'designation'   => 'required',
            'emp_id'        => 'required|integer|min:1|unique:user_inventory',
            'emp_mobile'    => 'required|min:10|max:10|unique:user_inventory',
            'emp_email'     => 'required|unique:user_inventory',
            'emp_gender'    => 'required|integer',
            'emp_dob'       => 'required',
            'user_login'    => 'required|integer|min:1|unique:user_inventory',
            'user_pwd'      => 'required',
            'status'        => 'required|boolean',
            'user_role_id'  => 'required|integer',
            'start_date'    => 'required',
            'is_test'       => 'required|boolean',
        ]);

        $newId = DB::table('user_inventory')->orderBy('user_inv_id', 'desc')->first('user_id');
        $userID = ($newId != null) ? $newId->user_id + 1 : 1;

        DB::table('user_inventory')->insert([
            'user_id'       => $userID,
            'first_name'    => $request->input('first_name'),
            'middle_name'   => $request->input('middle_name'),
            'last_name'     => $request->input('last_name'),
            'designation'   => $request->input('designation'),
            'emp_id'        => $request->input('emp_id'),
            'emp_mobile'    => $request->input('emp_mobile'),
            'emp_email'     => $request->input('emp_email'),
            'emp_gender'    => $request->input('emp_gender'),
            'emp_dob'       => $request->input('emp_dob'),
            'description'   => $request->input('description'),
            'user_login'    => $request->input('user_login'),
            'user_pwd'      => $request->input('user_pwd'),
            'status'        => $request->input('status'),
            'user_role_id'  => $request->input('user_role_id'),
            'start_date'    => $request->input('start_date'),
            'end_date'      => $request->input('end_date'),
            'is_test'       => $request->input('is_test'),
            'created_by'    => 1,
            'created_at'    => Carbon::now(),
        ]);

        $this->insertJson($userID);

        return redirect()
            ->to('users')
            ->with([
                'status'    => true,
                'message'   => ' USER CREATED SUCCESSFULLY.'
            ]);
    }

    public function edit($id)
    {

        $userRoles  = DB::table('ms_user_role')->get();
        $User       = DB::table('user_inventory')->where('user_id','=',$id)->first();

        return Inertia::render('User/Edit',[
            'userRoles' => $userRoles,
            'User'      => $User,
        ]);
    }

    public function update(Request $request,$userID): RedirectResponse
    {
        $request->validate([
            'first_name'    => 'required',
            'last_name'     => 'required',
            'designation'   => 'required',
            'emp_id'        => 'required|integer|min:1',
            'emp_mobile'    => 'required|min:10|max:10',
            'emp_email'     => 'required',
            'emp_gender'    => 'required|integer',
            'emp_dob'       => 'required',
            'user_login'    => 'required|integer|min:1',
            'user_pwd'      => 'required',
            'status'        => 'required|boolean',
            'user_role_id'  => 'required|integer',
            'start_date'    => 'required',
            'is_test'       => 'required|boolean',
        ]);

        DB::table('user_inventory')->where('user_id','=',$userID)->update([
            'first_name'    => $request->input('first_name'),
            'middle_name'   => $request->input('middle_name'),
            'last_name'     => $request->input('last_name'),
            'designation'   => $request->input('designation'),
            'emp_id'        => $request->input('emp_id'),
            'emp_mobile'    => $request->input('emp_mobile'),
            'emp_email'     => $request->input('emp_email'),
            'emp_gender'    => $request->input('emp_gender'),
            'emp_dob'       => $request->input('emp_dob'),
            'description'   => $request->input('description'),
            'user_login'    => $request->input('user_login'),
            'user_pwd'      => $request->input('user_pwd'),
            'status'        => $request->input('status'),
            'user_role_id'  => $request->input('user_role_id'),
            'start_date'    => $request->input('start_date'),
            'end_date'      => $request->input('end_date'),
            'is_test'       => $request->input('is_test'),
            'modified_by'   => 1,
            'updated_at'    => now(),
        ]);

        $this->insertJson($userID);

        return redirect()
            ->to('users')
            ->with([
                'status' => true,
                'message' => ' USER UPDATED SUCCESSFULLY.'
            ]);
    }

    /* USER DATA IN FORM OF JSON */
    private function insertJson($userID)
    {
        $userData = DB::table('user_inventory')
            ->where('user_id', '=', $userID)
            ->first(['user_id', 'first_name', 'middle_name', 'last_name', 'designation', 'emp_id', 'emp_mobile', 'emp_email', 'emp_gender', 'emp_dob', 'description', 'user_login', 'user_pwd', 'status', 'user_role_id', 'user_data', 'start_date', 'end_date', 'is_test', 'created_by', 'modified_by', 'created_at', 'updated_at']);

        DB::table('user_inventory')
            ->where('user_id', '=', $userID)
            ->update([
                'user_data' => json_encode($userData)
            ]);
    }
}
