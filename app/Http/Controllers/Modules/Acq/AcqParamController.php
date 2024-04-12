<?php

namespace App\Http\Controllers\Modules\Acq;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AcqParamController extends Controller
{

    /* FOR SHOWING ACQUIRER DATA */
    public function index(): Response
    {
        $acqParams = DB::table('acq_param')
            ->join('ms_line_inventory as li','li.line_id','=','acq_param.line_id')
            ->select(['acq_param.*','li.line_name'])
            ->orderBy('acq_param_id','ASC')
            ->get();

        return Inertia::render('Acq/Index', [
            'acqParams'=>$acqParams
        ]);

    }

    /* WHILE CREATING ACQUIRING PARAMETERS */
    public function create(): Response
    {
        $Lines = DB::table('ms_line_inventory')->get();
        $EmvTypes = DB::table('ms_emv_type')->get();


        return Inertia::render('Acq/Create', [
            'Lines'     => $Lines,
            'EmvTypes'     => $EmvTypes,
        ]);

    }

    /* INSERTING ACQUIRER DATA */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'operator_id'   =>'required',
            'acq_id'        =>'required',
            'acq_name'      =>'required',
            'acq_mid'       =>'required',
            'client_id'     =>'required|unique:acq_param',
            'line_id'       =>'required|integer|min:0',
        ]);

        $newId = DB::table('acq_param')->orderBy('acq_param_id', 'desc')->first('acq_param_id');
        $acqParamID = ($newId != null) ? $newId->acq_param_id + 1 : 1;

        /*FOR GATE*/
        if ($request->input('emv_type_id') == 1) {
            DB::table('acq_param')->insert([
                'operator_id'   => $request->input('operator_id'),
                'acq_id'        => $request->input('acq_id'),
                'acq_name'      => $request->input('acq_name'),
                'acq_mid'       => $request->input('acq_mid'),
                'client_id'     => $request->input('client_id'),
                'line_id'       => $request->input('line_id'),
                'description'   => $request->input('description'),
                'eq_type_id'    =>7,
                'created_at'    => now()
            ]);
        }

        /*FOR TOM*/
        if ($request->input('emv_type_id') == 2) {
            DB::table('acq_param')->insert([
                'operator_id'   => $request->input('operator_id'),
                'acq_id'        => $request->input('acq_id'),
                'acq_name'      => $request->input('acq_name'),
                'acq_mid'       => $request->input('acq_mid'),
                'client_id'     => $request->input('client_id'),
                'line_id'       => $request->input('line_id'),
                'description'   => $request->input('description'),
                'eq_type_id'    =>6,
                'created_at'    => now()
            ]);
        }

        return redirect('acq') ->with([
            'message' => $request->input('acq_name') . ' ACQUIRER CREATED SUCCESSFULLY.'
        ]);
    }

    /* WHILE EDITING ACQUIRER DATA */
    public function edit($id): Response
    {

        $acqParam = DB::table('acq_param')
            ->where('acq_param_id','=',$id)
            ->join('ms_line_inventory as li','li.line_id','=','acq_param.line_id')
            ->select(['acq_param.*','li.line_name'])
            ->first();

        $Lines      = DB::table('ms_line_inventory')->get();
        $EmvTypes   = DB::table('ms_emv_type')->get();


        return Inertia::render('Acq/Edit', [
            'acqParam'  => $acqParam,
            'Lines'     => $Lines,
            'EmvTypes'  => $EmvTypes,
        ]);
    }

    /* UPDATING ACQUIRER DATA */
    public function update(Request $request, $id): Redirector|Application|RedirectResponse
    {

         $request->validate([
            'operator_id'   =>'required',
            'acq_id'        =>'required',
            'acq_name'      =>'required',
            'acq_mid'       =>'required',
            'client_id'     =>'required',
            'line_id'       =>'required|integer|min:0',
        ]);

        DB::table('acq_param')
            ->where('acq_param_id','=',$id)
            ->update([
            'operator_id' => $request->input('operator_id'),
            'acq_id'      => $request->input('acq_id'),
            'acq_name'    => $request->input('acq_name'),
            'acq_mid'     => $request->input('acq_mid'),
            'client_id'   => $request->input('client_id'),
            'line_id'     => $request->input('line_id'),
            'description' => $request->input('description'),
            'updated_at'  => now()
        ]);


        return redirect('acq')->with([
            'message' => $request->input('acq_name'). ' ACQUIRER UPDATED SUCCESSFULLY.'
        ]);

    }

}
