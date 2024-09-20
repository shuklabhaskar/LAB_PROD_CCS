<?php

namespace App\Http\Controllers\Modules\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class OperatorController extends Controller
{
    public function index()
    {
        $Operators = DB::table("ms_operators")->get();

        return Inertia::render('Operator/Index', [
            'operators' => $Operators
        ]);
    }

    /* TO VIEW CREATE PAGE */
    public function create()
    {
        return Inertia::render('Operator/Create');
    }

    /* TO CREATE OPERATOR */
    public function store(Request $request)
    {
        $request->validate([
            'operator_id'   => 'required',
            'operator_name' => 'required',
            'user_name'     => 'required',
            'user_password' => [
                'required',
                'string',
                'min:8',             /*MIN LENGTH 8 CHAR*/
                'max:20',            /*MAX LENGTH 20 CHAR*/
                'regex:/[a-z]/',     /*AT LEAST ONE LOWER CASE*/
                'regex:/[A-Z]/',     /*AT LEAST ONE UPPER CASE*/
                'regex:/[0-9]/',     /*AT LEAST ONE NUMERIC*/
                'regex:/[@$!%*?&#]/' /*AT LEAST ONE SPECIAL CHAR*/
            ]
        ]);

        DB::table('ms_operators')
            ->insert([
                'operator_id'   => $request->input('operator_id'),
                'operator_name' => $request->input('operator_name'),
                'user_name'     => $request->input('user_name'),
                'user_password' => Hash::make($request->input('user_password')),
            ]);

        return redirect()
            ->to('operators')
            ->with([
                'message' => 'OPERATOR CREATED SUCCESSFULLY.'
            ]);

    }

    /* TO SHOW DATA OF OPERATOR WHILE EDITING */
    public function edit($id)
    {
        $Operator = DB::table("ms_operators")
            ->where("operator_id",'=', $id)
            ->first();

        return Inertia::render('Operator/Edit', [
            'Operator' => $Operator
        ]);

    }

    /* TO UPDATE RESPECTIVE OPERATOR */
    public function update(Request $request)
    {
        $request->validate([
            'operator_id'   => 'required',
            'operator_name' => 'required',
            'user_name'     => 'required',
            'user_password' => 'required',
        ]);

        DB::table('ms_operators')
            ->where('operator_id',$request->input('operator_id'))
            ->update([
            'operator_name' => $request->input('operator_name'),
            'user_name'     => $request->input('user_name'),
            'updated_at'    => now(),
        ]);

        return redirect()
            ->to('operators')
            ->with([
                'message' => 'OPERATOR UPDATED SUCCESSFULLY.'
            ]);

    }

    /* TO UPDATE PASSWORD OF RESPECTIVE OPERATOR */
    public function passwordUpdate(Request $request){

        $request->validate([
            'user_password' => [
                'required',
                'string',
                'min:8',             /*MIN LENGTH 8 CHAR*/
                'max:20',            /*MAX LENGTH 20 CHAR*/
                'regex:/[a-z]/',     /*AT LEAST ONE LOWER CASE*/
                'regex:/[A-Z]/',     /*AT LEAST ONE UPPER CASE*/
                'regex:/[0-9]/',     /*AT LEAST ONE NUMERIC*/
                'regex:/[@$!%*?&#]/' /*AT LEAST ONE SPECIAL CHAR*/
            ]
        ]);

        DB::table('ms_operators')
            ->where('operator_id',$request->input('operator_id'))
            ->update([
            'user_password' => Hash::make($request->input('user_password')),
            'updated_at'    => now(),
        ]);

        return redirect()
            ->to('operators')
            ->with([
                'message' => 'OPERATOR PASSWORD UPDATED SUCCESSFULLY.'
            ]);
    }

}
