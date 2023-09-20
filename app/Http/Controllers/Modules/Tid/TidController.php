<?php

namespace App\Http\Controllers\Modules\Tid;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TidController extends Controller
{
    public function index()
    {
        $Tids = DB::table('tid_inv as ti')
            ->join('ms_emv_type as met','met.emv_type_id','=','ti.emv_type_id')
            ->join('acq_param as ap','ap.eq_type_id','=','ti.eq_type_id')
            ->get();

        return Inertia::render('Tid_Inventory/Index',[
            'Tids'=>$Tids
        ]);

    }

    public function create(){

        $EmvTypes = DB::table('ms_emv_type')->get();
        $Acquirers = DB::table('acq_param')->get();

        return Inertia::render('Tid_Inventory/Create',[
            'EmvTypes'  =>$EmvTypes,
            'Acquirers' =>$Acquirers,
        ]);

    }

    public function store(Request $request)
    {
        $request->validate([
            'emv_tid'           => 'required|unique:tid_inv',
            'emv_serial_no'     => 'required|unique:tid_inv',
            'emv_type_id'       => 'required|integer',
            'acq_id'            => 'required|integer',
        ]);

        /* FOR GATE */
        if ($request->input('emv_type_id') == 1){
            DB::table('tid_inv')->insert([
                'emv_tid'           =>$request->input('emv_tid'),
                'emv_serial_no'     =>$request->input('emv_serial_no'),
                'emv_box_serial_no' =>$request->input('device_id'),
                'emv_type_id'       =>$request->input('emv_type_id'),
                'eq_type_id'        =>7,
                'acq_id'            =>$request->input('acq_id'),
                'start_date'        =>date('Y-m-d H:i:s'),
                'is_bind'           =>false,
            ]);
        }

        /* FOR TOM */
        if ($request->input('emv_type_id') == 2){
            DB::table('tid_inv')->insert([
                'emv_tid'           =>$request->input('emv_tid'),
                'emv_serial_no'     =>$request->input('emv_serial_no'),
                'emv_box_serial_no' =>$request->input('device_id'),
                'emv_type_id'       =>$request->input('emv_type_id'),
                'acq_id'            =>$request->input('acq_id'),
                'eq_type_id'        =>6,
                'start_date'        =>date('Y-m-d H:i:s'),
                'is_bind'           =>false,
            ]);
        }


        return redirect()
            ->to('tids')
            ->with([
                'status'    => true,
                'message'   => ' TID CREATED SUCCESSFULLY.'
            ]);

    }

    public function edit($id){

        $EmvTypes = DB::table('ms_emv_type')->get();
        $Acquirers = DB::table('acq_param')->get();
        $tid        = DB::table('tid_inv')->where('tid_inv_id','=',$id)->first();

        return Inertia::render('Tid_Inventory/Edit',[
            'EmvTypes'  =>$EmvTypes,
            'Acquirers' =>$Acquirers,
            'tid'       =>$tid,
        ]);

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'emv_tid'           => 'required',
            'emv_serial_no'     => 'required',
            'acq_id'            => 'required|integer',
        ]);

        DB::table('tid_inv')
            ->where('tid_inv_id','=',$id)
            ->update([
            'emv_tid'           =>$request->input('emv_tid'),
            'emv_serial_no'     =>$request->input('emv_serial_no'),
            'emv_box_serial_no' =>$request->input('device_id'),
            'acq_id'            =>$request->input('acq_id'),
            'start_date'        =>now(),
            'is_bind'           =>false,
        ]);

        return redirect()
            ->to('tids')
            ->with([
                'status' => true,
                'message' => 'TID UPDATED SUCCESSFULLY.'
            ]);

    }

}
