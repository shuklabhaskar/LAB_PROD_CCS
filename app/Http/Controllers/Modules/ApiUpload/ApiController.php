<?php

namespace App\Http\Controllers\Modules\ApiUpload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ApiController extends Controller
{
    /*TO LOAD DATA ON INDEX PAGE*/
    public function index()
    {
        $apis = DB::table('ms_api_routes as apis')
            ->join('ms_product_type', 'ms_product_type.product_type_id', '=', 'apis.product_type_id')
            ->select([
                'apis.ms_api_route_id',
                'apis.api_name',
                'apis.api_description',
                'apis.api_request_type',
                'apis.product_type_id',
                'ms_product_type.product_name',
            ])
            ->get();

        return Inertia::render('ApiUpload/Index', [
            'apis' => $apis
        ]);

    }

    /* CREATE VIEW */
    public function create(){

        $ProductTypes = DB::table('ms_product_type')
            ->orderBy('product_type_id', 'ASC')
            ->get();

        return Inertia::render('ApiUpload/Create', [
            'ProductTypes' => $ProductTypes,
        ]);

    }

    /* INSERT THE DATA IN DATABASE FROM REQUEST*/
    public function store(Request $request)
    {
        /*VALIDATING REQUEST INPUTS*/
        $request->validate([
            'api_name'          => 'required|string',
            'api_route'         => 'required|string|unique:ms_api_routes',
            'api_request_type'  => 'required|integer',
            'product_type_id'   => 'required|integer',
        ]);

        /* INSERTING REQUEST INPUTS IN DATABASE*/
        DB::table('ms_api_routes')->insert([
            'api_name'          => $request->input('api_name'),
            'api_route'         => $request->input('api_route'),
            'api_description'   => $request->input('api_description'),
            'api_request_type'  => $request->input('api_request_type'),
            'product_type_id'   => $request->input('product_type_id'),
            'created_at'        => now(),
        ]);

        /* REDIRECTING TO MAIN PAGE */
        return redirect()
            ->to('api/endPoint')
            ->with([
                'message' => 'API CREATED SUCCESSFULLY.'
            ]);

    }

    /* GETTING DATA OF RESPECTIVE API USING ID*/
    public function edit($id)
    {
        $api = DB::table('ms_api_routes')
                ->where('ms_api_route_id','=', $id)
                ->first();

        $ProductTypes = DB::table('ms_product_type')
            ->orderBy('product_type_id', 'ASC')
            ->get();

        return Inertia::render('ApiUpload/Edit', [
            'ProductTypes'  => $ProductTypes,
            'api'           => $api
        ]);

    }

    /*UPDATING THE DATA IN DATABASE FOR RESPECTIVE ID*/
    public function update(Request $request,$id)
    {

        /*VALIDATING REQUEST INPUTS*/
        $request->validate([
            'api_name'          => 'required|string',
            'api_route'         => 'required|string',
            'api_request_type'  => 'required|integer',
            'product_type_id'   => 'required|integer',
        ]);

        /* UPDATING REQUEST INPUTS IN DATABASE*/
        DB::table('ms_api_routes')
            ->where('ms_api_route_id','=', $id)
            ->update([
                'api_name'          => $request->input('api_name'),
                'api_route'         => $request->input('api_route'),
                'api_description'   => $request->input('api_description'),
                'api_request_type'  => $request->input('api_request_type'),
                'product_type_id'   => $request->input('product_type_id'),
                'updated_at'        => now(),
            ]);

        /* REDIRECTING TO MAIN PAGE */
        return redirect()
            ->to('api/endPoint')
            ->with([
                'message' => 'API UPDATED SUCCESSFULLY.'
            ]);

    }

}
