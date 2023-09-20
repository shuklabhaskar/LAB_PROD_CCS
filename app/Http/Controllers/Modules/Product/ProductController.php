<?php

namespace App\Http\Controllers\Modules\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /*MASTER DATA*/
    function index(){

        $Products = DB::table('ms_product_type')
            ->orderBy('product_type_id','ASC')
            ->get();

        return Inertia::render('Products/Index', [
            'Products' => $Products
        ]);
    }

    /* CREATE VIEW */
    function create(){
        return Inertia::render('Products/Create', [

        ]);
    }

    /* CREATING NEW PRODUCT */
    function store(Request $request)
    {
        $data = $request->validate([
            'product_name'      => 'required',
        ]);

        $productName =  Str::lower($request->input('product_name'));
        DB::table('ms_product_type')
            ->insert([
                'product_name' =>$productName,
                'description' => $request->input('description'),
            ]);

        return redirect()
            ->to('products')
            ->with([
                'message' => 'PRODUCT CREATED SUCCESSFULLY.'
            ]);
    }

    /* EDIT VIEW */
    function edit($id)
    {
        $Product = DB::table('ms_product_type')->where('product_type_id', '=', $id)->first();

        return Inertia::render('Products/Edit', [
            'Product'=>$Product
        ]);
    }

    /* UPDATE PRODUCT */
    function update(Request $request,$id)
    {
        $data = $request->validate([
            'product_name' => 'required',
        ]);

        $productName =  Str::lower($request->input('product_name'));

        DB::table('ms_product_type')
            ->where('product_type_id','=',$id)
            ->update([
                'product_name' =>$productName,
                'description' => $request->input('description'),
            ]);

        return redirect()
            ->to('products')
            ->with([
                'status' => true,
                'message' => $request->input('product_name') . ' PRODUCT UPDATED SUCCESSFULLY.'
            ]);
    }

}
