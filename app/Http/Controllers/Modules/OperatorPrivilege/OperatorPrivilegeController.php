<?php

namespace App\Http\Controllers\Modules\OperatorPrivilege;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OperatorPrivilegeController extends Controller
{
    public function index()
    {
        $operatorsPrivilegeList = DB::table('operators_api_privilege')
            ->join('ms_api_routes', 'ms_api_routes.ms_api_route_id', '=', 'operators_api_privilege.api_permitted')
            ->join('ms_operators', 'ms_operators.operator_id', '=', 'operators_api_privilege.operator_id')
            ->join('ms_product_type', 'ms_product_type.product_type_id', '=', 'ms_api_routes.product_type_id')
            ->where('is_activated','=',true)
            ->select([
                'operators_api_privilege.created_at',
                'ms_api_routes.api_name',
                'ms_api_routes.api_description',
                'ms_operators.operator_name',
                'operators_api_privilege.operators_api_prv_id',
                'ms_product_type.product_name',
            ])
            ->get();

        return Inertia::render('OperatorPrivilege/Index', [
            'operatorsPrivilegeList' => $operatorsPrivilegeList
        ]);
    }

    public function create()
    {
        $Operators = DB::table('ms_operators')
            ->select([
                'operator_id',
                'operator_name',
            ])->get();

        $apiList = DB::table('ms_api_routes')
            ->join('ms_product_type', 'ms_product_type.product_type_id', '=', 'ms_api_routes.product_type_id')
            ->select([
                'ms_api_route_id',
                'api_name',
                'api_description',
                'ms_product_type.product_type_id',
                'ms_product_type.product_name',
                'created_at',
            ])->get();

        return Inertia::render('OperatorPrivilege/Create', [
            'Operators' => $Operators,
            'apiList' => $apiList
        ]);
    }

    public function store(Request $request)
    {
        /*VALIDATING REQUEST INPUTS*/
        $request->validate([
            'operator_id'         => 'required|integer',
        ]);

        $id = $request->input('operator_id');
        $apiList = $request->selected;

        foreach ($apiList as $api) {

            $record = DB::table('operators_api_privilege')
                ->where('operator_id', $id)
                ->where('api_permitted', $api)
                ->first();

            if ($record) {

                DB::table('operators_api_privilege')
                    ->where('operator_id', $id)
                    ->where('api_permitted', $api)
                    ->updateOrInsert([
                        'operator_id'   => $request->input('operator_id'),
                        'api_permitted' => $api,
                        'is_activated'  => true,
                        'updated_at'    => now(),
                        'created_at'    => now()
                    ]);

            }else{

                DB::table('operators_api_privilege')
                    ->where('operator_id', $id)
                    ->where('api_permitted', $api)
                    ->updateOrInsert([
                        'operator_id'   => $id,
                        'api_permitted' => $api,
                        'is_activated'  => true,
                        'created_at'    => now()
                    ]);
            }

        }

        return redirect()
            ->to('/operators/privilege')
            ->with('message', 'API PRIVILEGE HAS BEEN CREATED SUCCESSFULLY');

    }

    public function edit($id)
    {
        /*FOR DROP DOWN OF OPERATORS */
        $operatorId = DB::table('operators_api_privilege')
            ->where('operators_api_prv_id', $id)
            ->join('ms_operators', 'ms_operators.operator_id', '=', 'operators_api_privilege.operator_id')
            ->first();

        $Operators = DB::table('ms_operators')->get();

        $apiPermitted = DB::table('operators_api_privilege')
            ->where('operator_id', $operatorId->operator_id)
            ->where('is_activated', true)
            ->pluck('api_permitted')
            ->toArray();

        $selectedApi = DB::table('ms_api_routes')
            ->whereIn('ms_api_route_id', $apiPermitted)
            ->join('ms_product_type', 'ms_product_type.product_type_id', '=', 'ms_api_routes.product_type_id')
            ->get();

        $apiList = DB::table('ms_api_routes')
            ->join('ms_product_type', 'ms_product_type.product_type_id', '=', 'ms_api_routes.product_type_id')
            ->get();

        return Inertia::render('OperatorPrivilege/Edit', [
            'operatorId'=> $operatorId,
            'Operators' => $Operators,
            'apiList' => $apiList,
            'selectedApi' => $selectedApi,
        ]);

    }

    public function update(Request $request, $id)
    {

        $apiList = $request->selected;

        DB::table('operators_api_privilege')->where('operator_id', $id)->update([
           'is_activated' => false,
        ]);

        foreach ($apiList as $api) {

            $record = DB::table('operators_api_privilege')
                ->where('operator_id', $id)
                ->where('api_permitted', $api)
                ->first();

            if ($record) {

                DB::table('operators_api_privilege')
                    ->where('operator_id', $id)
                    ->where('api_permitted', $api)
                    ->updateOrInsert([
                        'operator_id'   => $request->input('operator_id'),
                        'api_permitted' => $api,
                        'is_activated'  => true,
                        'updated_at'    => now(),
                        'created_at'    => now()
                    ]);

            }else{

                DB::table('operators_api_privilege')
                    ->where('operator_id', $id)
                    ->where('api_permitted', $api)
                    ->updateOrInsert([
                        'operator_id'   => $request->input('operator_id'),
                        'api_permitted' => $api,
                        'is_activated'  => true,
                        'created_at'    => now()
                    ]);
            }

        }

        return redirect()
            ->to('/operators/privilege')
            ->with('message', 'API PRIVILEGE HAS BEEN UPDATED SUCCESSFULLY');

    }

}

