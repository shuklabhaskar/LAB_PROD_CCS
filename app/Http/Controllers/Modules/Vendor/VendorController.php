<?php

namespace App\Http\Controllers\Modules\Vendor;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class VendorController extends Controller
{
    public function  index(){

        $Vendors = DB::table('ms_vendors')
            ->orderBy('vendor_id','ASC')
            ->get();

        return Inertia::render('Vendors/Index', [
            'Vendors'=>$Vendors
        ]);
    }

    /* CREATE VIEW */
    function create(){
        return Inertia::render('Vendors/Create', [

        ]);
    }

    /* CREATING NEW VENDOR */
    function store(Request $request)
    {
        $request->validate([
            'vendor_name'      => 'required',
        ]);

        $vendor_name =  Str::lower($request->input('vendor_name'));

        DB::table('ms_vendors')
            ->insert([
                'vendor_name' =>$vendor_name,
                'description' => $request->input('description'),
            ]);

        return redirect()
            ->to('vendors')
            ->with([
                'message' => 'VENDOR CREATED SUCCESSFULLY.'
            ]);
    }

    /* EDIT VIEW */
    function edit($id)
    {
        $Vendor = DB::table('ms_vendors')->where('vendor_id', '=', $id)->first();

        return Inertia::render('Vendors/Edit', [
            'Vendor'=>$Vendor
        ]);
    }

    /* UPDATE VENDOR */
    function update(Request $request,$id)
    {
        $request->validate([
            'vendor_name' => 'required',
        ]);

        $vendor_name =  Str::lower($request->input('vendor_name'));

        DB::table('ms_vendors')
            ->where('vendor_id','=',$id)
            ->update([
                'vendor_name' =>$vendor_name,
                'description' => $request->input('description'),
                'updated_at'=> Carbon::now()
            ]);

        return redirect()
            ->to('vendors')
            ->with([
                'status' => true,
                'message' => $request->input('vendor_name') . ' VENDOR UPDATED SUCCESSFULLY.'
            ]);
    }
}
