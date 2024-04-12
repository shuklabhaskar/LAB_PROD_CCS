<?php

namespace App\Http\Controllers\Modules\Pass;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class PassController extends Controller
{
    /* INDEX PAGE */
    function index()
    {
        $Passes = DB::table('pass_inventory as pi')
            ->join('ms_product_type as pt','pt.product_type_id','=','pi.product_id')
            ->join('ms_media_types as tt','tt.media_type_id','=','pi.media_type_id')
            ->orderBy('pass_inv_id','ASC')
            ->select(['pi.*','pt.product_name','tt.media_name'])
            ->get();

        return Inertia::render('Pass/Index', [
            'Passes'=>$Passes
        ]);
    }

    /* FOR CREATING NEW PASS */
    function create()
    {

        $MediaTypes     = DB::table('ms_media_types')->get();
        $Companies      = DB::table('ms_company_inv')->get();
        $ProductTypes   = DB::table('ms_product_type')->orderBy('product_type_id','ASC')->get();
        $Units          = DB::table('ms_units')->get();
        $Fares          = DB::table('fare_inventory')->select(['fare_name','fare_table_id'])->get();
        $cardTypes      = DB::table('card_type')->get();

        return Inertia::render('Pass/Create', [
            'MediaTypes'    =>$MediaTypes,
            'Companies'     =>$Companies,
            'ProductTypes'  =>$ProductTypes,
            'Units'         =>$Units,
            'Fares'         =>$Fares,
            'cardTypes'     =>$cardTypes,
        ]);

    }

    /* STORE NEW PASS */
    function store(Request $request)
    {
        $request->validate([
            'media_type_id'  =>'required|integer|min:0',
        ]);

        if ($request->input('media_type_id') == 4 || $request->input('media_type_id') == 3){

            $request->validate([
                'media_type_id'  =>'required|integer|min:0',
                'product_type_id'=>'required|integer|min:0',
                'pass_id'        =>'required|unique:pass_inventory',
                'pass_name'      =>'required',
                'company_id'     =>'required|integer|min:0',
                'status'         =>'required|boolean',
                'fare_table_id'  =>'required|integer|min:0',
                'start_date'     =>'required',
            ]);

            DB::table('pass_inventory')->insert([
                'media_type_id'                => $request->input('media_type_id'),
                'product_id'                   => $request->input('product_type_id'),
                'pass_id'                      => $request->input('pass_id'),
                'pass_name'                    => $request->input('pass_name'),
                'description'                  => $request->input('description'),
                'company_id'                   => $request->input('company_id'),
                'status'                       => $request->input('status'),
                'fare_table_id'                => $request->input('fare_table_id'),
                'start_date'                   => $request->input('start_date'),
                'end_date'                     => $request->input('end_date'),
                'same_stn_over_time_limit'     => $request->input('same_stn_over_time_limit'),
                'same_stn_over_time_pen'       => $request->input('same_stn_over_time_pen'),
                'same_stn_over_time_max_pen'   => $request->input('same_stn_over_time_max_pen'),
                'other_stn_over_time_limit'    => $request->input('other_stn_over_time_limit'),
                'other_stn_over_time_pen'      => $request->input('other_stn_over_time_pen'),
                'other_stn_over_time_max_pen'  => $request->input('other_stn_over_time_max_pen'),
                'over_travel_pen'              => $request->input('over_travel_pen'),
                'entry_mismatch_limit'         => $request->input('entry_mismatch_limit'),
                'entry_mismatch_same_time_pen' => $request->input('entry_mismatch_same_time_pen'),
                'entry_mismatch_no_exit_pen'   => $request->input('entry_mismatch_no_exit_pen'),
                'exit_mismatch_pen'            => $request->input('exit_mismatch_pen'),
                'entry_exit_control'           => $request->input('entry_exit_control'),
                'is_test'                      => $request->input('is_test'),
                'entry_validity_period'        => $request->input('entry_validity_period'),
                'return_validity_period'       => $request->input('return_validity_period'),
                'pass_validity_period'         => $request->input('pass_validity_period'),
                'grace_period'                 => $request->input('grace_period'),
                'trip_count'                   => $request->input('trip_count'),
                'daily_trip_limit'             => $request->input('daily_trip_limit'),
                'issue_permit'                 => $request->input('issue_permit'),
                'reload_permit'                => $request->input('reload_permit'),
                'refund_permit'                => $request->input('refund_permit'),
                'refund_charges'               => $request->input('refund_charges'),
                'min_sv_topup_amt'             => $request->input('min_sv_topup_amt'),
                'sv_step_topup_amt'            => $request->input('sv_step_topup_amt'),
                'min_sv_entry_bal'             => $request->input('min_sv_entry_bal'),
                'max_sv_bal'                   => $request->input('max_sv_bal'),
                'created_date'                 => now(),
            ]);

        }

        if ($request->input('media_type_id') == 1 || $request->input('media_type_id') == 2 ) {

            $request->validate([
                'media_type_id'  =>'required|integer|min:0',
                'card_type_id'   =>'required|integer|min:0',
                'product_type_id'=>'required|integer|min:0',
                'pass_id'        =>'required',
                'pass_name'      =>'required',
                'company_id'     =>'required|integer|min:0',
                'status'         =>'required|boolean',
                'fare_table_id'  =>'required|integer|min:0',
                'start_date'     =>'required',
            ]);

            DB::table('pass_inventory')->insert([
                'media_type_id'                 => $request->input('media_type_id'),
                'card_type_id'                  => $request->input('card_type_id'),
                'product_id'                    => $request->input('product_type_id'),
                'pass_id'                       => $request->input('pass_id'),
                'pass_name'                     => $request->input('pass_name'),
                'description'                   => $request->input('description'),
                'company_id'                    => $request->input('company_id'),
                'status'                        => $request->input('status'),
                'fare_table_id'                 => $request->input('fare_table_id'),
                'start_date'                    => $request->input('start_date'),
                'end_date'                      => $request->input('end_date'),
                'same_stn_over_time_limit'      => $request->input('same_stn_over_time_limit'),
                'same_stn_over_time_pen'        => $request->input('same_stn_over_time_pen'),
                'same_stn_over_time_max_pen'    => $request->input('same_stn_over_time_max_pen'),
                'other_stn_over_time_limit'     => $request->input('other_stn_over_time_limit'),
                'other_stn_over_time_pen'       => $request->input('other_stn_over_time_pen'),
                'other_stn_over_time_max_pen'   => $request->input('other_stn_over_time_max_pen'),
                'over_travel_pen'               => $request->input('over_travel_pen'),
                'entry_mismatch_limit'          => $request->input('entry_mismatch_limit'),
                'entry_mismatch_same_time_pen'  => $request->input('entry_mismatch_same_time_pen'),
                'entry_mismatch_no_exit_pen'    => $request->input('entry_mismatch_no_exit_pen'),
                'exit_mismatch_pen'             => $request->input('exit_mismatch_pen'),
                'entry_exit_control'            => $request->input('entry_exit_control'),
                'is_test'                       => $request->input('is_test'),
                'entry_validity_period'         => $request->input('entry_validity_period'),
                'return_validity_period'        => $request->input('return_validity_period'),
                'pass_validity_period'          => $request->input('pass_validity_period'),
                'grace_period'                  => $request->input('grace_period'),
                'trip_count'                    => $request->input('trip_count'),
                'daily_trip_limit'              => $request->input('daily_trip_limit'),
                'issue_permit'                  => $request->input('issue_permit'),
                'reload_permit'                 => $request->input('reload_permit'),
                'refund_permit'                 => $request->input('refund_permit'),
                'refund_charges'                => $request->input('refund_charges'),
                'min_sv_topup_amt'              => $request->input('min_sv_topup_amt'),
                'sv_step_topup_amt'             => $request->input('sv_step_topup_amt'),
                'min_sv_entry_bal'              => $request->input('min_sv_entry_bal'),
                'max_sv_bal'                    => $request->input('max_sv_bal'),
                'created_date'                  => now(),
            ]);
        }

        return redirect('pass')
            ->with([
                'status' => true,
                'message' => $request->input('pass_name') . ' PASS CREATED SUCCESSFULLY.'
            ]);
    }

    /* EDIT VIEW */
    function edit($id)
    {
        $MediaTypes     = DB::table('ms_media_types')->get();
        $Companies      = DB::table('ms_company_inv')->get();
        $ProductTypes   = DB::table('ms_product_type')->orderBy('product_type_id','ASC')->get();

        $mediaTypeID           = DB::table('pass_inventory')
            ->where('pass_inv_id','=',$id)
            ->get('media_type_id')
            ->first();

        if ($mediaTypeID->media_type_id == 1 || $mediaTypeID->media_type_id == 2){

            $Pass = DB::table('pass_inventory as pi')
                ->join('card_type as ct','ct.card_type_id','=','pi.card_type_id')
                ->join('ms_media_types as mt','mt.media_type_id','=','pi.media_type_id')
                ->where('pi.pass_inv_id','=',$id)->first();
        }

        if ($mediaTypeID->media_type_id == 3 || $mediaTypeID->media_type_id == 4 || $mediaTypeID->media_type_id == 5){
            $Pass = DB::table('pass_inventory')
                ->where('pass_inv_id','=',$id)->first();
        }

        $Units      = DB::table('ms_units')->get();
        $Fares      = DB::table('fare_inventory')->select(['fare_name','fare_table_id'])->get();
        $cardTypes  = DB::table('card_type')->get();

        return Inertia::render('Pass/Edit', [
            'Pass'          => $Pass,
            'MediaTypes'    => $MediaTypes,
            'Companies'     => $Companies,
            'ProductTypes'  => $ProductTypes,
            'Units'         => $Units,
            'Fares'         => $Fares,
            'cardTypes'     => $cardTypes,
        ]);

    }

    /* UPDATE PASS */
    function update(Request $request,$id)
    {
        /* FOR OLC */
        if($request->input('media_type_id') == 1){

            /* VALIDATIONS */
            $request->validate([
                'media_type_id'  =>'required|integer|min:0',
                'card_type_id'   =>'required|integer|min:0',
                'product_type_id'=>'required|integer|min:0',
                'pass_id'        =>'required',
                'pass_name'      =>'required',
                'company_id'     =>'required|integer|min:0',
                'status'         =>'required|boolean',
                'fare_table_id'  =>'required|integer|min:0',
                'start_date'     =>'required',
            ]);

            /* UPDATE QUERY */
            DB::table('pass_inventory')
                ->where('pass_inv_id','=',$id)
                ->update([
                    'media_type_id'                => $request->input('media_type_id'),
                    'card_type_id'                 => $request->input('card_type_id'),
                    'product_id'                   => $request->input('product_type_id'),
                    'pass_id'                      => $request->input('pass_id'),
                    'pass_name'                    => $request->input('pass_name'),
                    'description'                  => $request->input('description'),
                    'company_id'                   => $request->input('company_id'),
                    'status'                       => $request->input('status'),
                    'fare_table_id'                => $request->input('fare_table_id'),
                    'start_date'                   => $request->input('start_date'),
                    'end_date'                     => $request->input('end_date'),
                    'same_stn_over_time_limit'     => $request->input('same_stn_over_time_limit'),
                    'same_stn_over_time_pen'       => $request->input('same_stn_over_time_pen'),
                    'same_stn_over_time_max_pen'   => $request->input('same_stn_over_time_max_pen'),
                    'other_stn_over_time_limit'    => $request->input('other_stn_over_time_limit'),
                    'other_stn_over_time_pen'      => $request->input('other_stn_over_time_pen'),
                    'other_stn_over_time_max_pen'  => $request->input('other_stn_over_time_max_pen'),
                    'over_travel_pen'              => $request->input('over_travel_pen'),
                    'entry_mismatch_limit'         => $request->input('entry_mismatch_limit'),
                    'entry_mismatch_same_time_pen' => $request->input('entry_mismatch_same_time_pen'),
                    'entry_mismatch_no_exit_pen'   => $request->input('entry_mismatch_no_exit_pen'),
                    'exit_mismatch_pen'            => $request->input('exit_mismatch_pen'),
                    'entry_exit_control'           => $request->input('entry_exit_control'),
                    'is_test'                      => $request->input('is_test'),
                    'entry_validity_period'        => $request->input('entry_validity_period'),
                    'return_validity_period'       => $request->input('return_validity_period'),
                    'pass_validity_period'         => $request->input('pass_validity_period'),
                    'grace_period'                 => $request->input('grace_period'),
                    'trip_count'                   => $request->input('trip_count'),
                    'daily_trip_limit'             => $request->input('daily_trip_limit'),
                    'issue_permit'                 => $request->input('issue_permit'),
                    'reload_permit'                => $request->input('reload_permit'),
                    'refund_permit'                => $request->input('refund_permit'),
                    'refund_charges'               => $request->input('refund_charges'),
                    'min_sv_topup_amt'             => $request->input('min_sv_topup_amt'),
                    'sv_step_topup_amt'            => $request->input('sv_step_topup_amt'),
                    'min_sv_entry_bal'             => $request->input('min_sv_entry_bal'),
                    'max_sv_bal'                   => $request->input('max_sv_bal'),
                    'updated_date'                 => now(),
                    'updated_by'                   => 1,
                ]);
        }

        /* FOR CLC */
        if($request->input('media_type_id') == 2){

            /* VALIDATIONS */
            $request->validate([
                'media_type_id'  =>'required|integer|min:0',
                'card_type_id'   =>'required|integer|min:0',
                'product_type_id'=>'required|integer|min:0',
                'pass_id'        =>'required',
                'pass_name'      =>'required',
                'company_id'     =>'required|integer|min:0',
                'status'         =>'required|boolean',
                'fare_table_id'  =>'required|integer|min:0',
                'start_date'     =>'required',
            ]);

            /* UPDATE QUERY */
            DB::table('pass_inventory')
                ->where('pass_inv_id','=',$id)
                ->update([
                    'media_type_id'                => $request->input('media_type_id'),
                    'card_type_id'                 => $request->input('card_type_id'),
                    'product_id'                   => $request->input('product_type_id'),
                    'pass_id'                      => $request->input('pass_id'),
                    'pass_name'                    => $request->input('pass_name'),
                    'description'                  => $request->input('description'),
                    'company_id'                   => $request->input('company_id'),
                    'status'                       => $request->input('status'),
                    'fare_table_id'                => $request->input('fare_table_id'),
                    'start_date'                   => $request->input('start_date'),
                    'end_date'                     => $request->input('end_date'),
                    'same_stn_over_time_limit'     => $request->input('same_stn_over_time_limit'),
                    'same_stn_over_time_pen'       => $request->input('same_stn_over_time_pen'),
                    'same_stn_over_time_max_pen'   => $request->input('same_stn_over_time_max_pen'),
                    'other_stn_over_time_limit'    => $request->input('other_stn_over_time_limit'),
                    'other_stn_over_time_pen'      => $request->input('other_stn_over_time_pen'),
                    'other_stn_over_time_max_pen'  => $request->input('other_stn_over_time_max_pen'),
                    'over_travel_pen'              => $request->input('over_travel_pen'),
                    'entry_mismatch_limit'         => $request->input('entry_mismatch_limit'),
                    'entry_mismatch_same_time_pen' => $request->input('entry_mismatch_same_time_pen'),
                    'entry_mismatch_no_exit_pen'   => $request->input('entry_mismatch_no_exit_pen'),
                    'exit_mismatch_pen'            => $request->input('exit_mismatch_pen'),
                    'entry_exit_control'           => $request->input('entry_exit_control'),
                    'is_test'                      => $request->input('is_test'),
                    'entry_validity_period'        => $request->input('entry_validity_period'),
                    'return_validity_period'       => $request->input('return_validity_period'),
                    'pass_validity_period'         => $request->input('pass_validity_period'),
                    'grace_period'                 => $request->input('grace_period'),
                    'trip_count'                   => $request->input('trip_count'),
                    'daily_trip_limit'             => $request->input('daily_trip_limit'),
                    'issue_permit'                 => $request->input('issue_permit'),
                    'reload_permit'                => $request->input('reload_permit'),
                    'refund_permit'                => $request->input('refund_permit'),
                    'refund_charges'               => $request->input('refund_charges'),
                    'min_sv_topup_amt'             => $request->input('min_sv_topup_amt'),
                    'sv_step_topup_amt'            => $request->input('sv_step_topup_amt'),
                    'min_sv_entry_bal'             => $request->input('min_sv_entry_bal'),
                    'max_sv_bal'                   => $request->input('max_sv_bal'),
                    'updated_date'                 => now(),
                    'updated_by'                   => 1,
                ]);
        }

        /* FOR MOBILE */
        if($request->input('media_type_id') == 3){

            /* VALIDATIONS */
             $request->validate([
                'media_type_id'  =>'required|integer|min:0',
                'product_type_id'=>'required|integer|min:0',
                'pass_id'        =>'required',
                'pass_name'      =>'required',
                'company_id'     =>'required|integer|min:0',
                'status'         =>'required|boolean',
                'fare_table_id'  =>'required|integer|min:0',
                'start_date'     =>'required',
            ]);

            /* UPDATE QUERY */
            DB::table('pass_inventory')
                ->where('pass_inv_id','=',$id)
                ->update([
                    'media_type_id'                => $request->input('media_type_id'),
                    'card_type_id'                 => null,
                    'product_id'                   => $request->input('product_type_id'),
                    'pass_id'                      => $request->input('pass_id'),
                    'pass_name'                    => $request->input('pass_name'),
                    'description'                  => $request->input('description'),
                    'company_id'                   => $request->input('company_id'),
                    'status'                       => $request->input('status'),
                    'fare_table_id'                => $request->input('fare_table_id'),
                    'start_date'                   => $request->input('start_date'),
                    'end_date'                     => $request->input('end_date'),
                    'same_stn_over_time_limit'     => $request->input('same_stn_over_time_limit'),
                    'same_stn_over_time_pen'       => $request->input('same_stn_over_time_pen'),
                    'same_stn_over_time_max_pen'   => $request->input('same_stn_over_time_max_pen'),
                    'other_stn_over_time_limit'    => $request->input('other_stn_over_time_limit'),
                    'other_stn_over_time_pen'      => $request->input('other_stn_over_time_pen'),
                    'other_stn_over_time_max_pen'  => $request->input('other_stn_over_time_max_pen'),
                    'over_travel_pen'              => $request->input('over_travel_pen'),
                    'entry_mismatch_limit'         => $request->input('entry_mismatch_limit'),
                    'entry_mismatch_same_time_pen' => $request->input('entry_mismatch_same_time_pen'),
                    'entry_mismatch_no_exit_pen'   => $request->input('entry_mismatch_no_exit_pen'),
                    'exit_mismatch_pen'            => $request->input('exit_mismatch_pen'),
                    'entry_exit_control'           => $request->input('entry_exit_control'),
                    'is_test'                      => $request->input('is_test'),
                    'entry_validity_period'        => $request->input('entry_validity_period'),
                    'return_validity_period'       => $request->input('return_validity_period'),
                    'pass_validity_period'         => $request->input('pass_validity_period'),
                    'grace_period'                 => $request->input('grace_period'),
                    'trip_count'                   => $request->input('trip_count'),
                    'daily_trip_limit'             => $request->input('daily_trip_limit'),
                    'issue_permit'                 => $request->input('issue_permit'),
                    'reload_permit'                => $request->input('reload_permit'),
                    'refund_permit'                => $request->input('refund_permit'),
                    'refund_charges'               => $request->input('refund_charges'),
                    'min_sv_topup_amt'             => $request->input('min_sv_topup_amt'),
                    'sv_step_topup_amt'            => $request->input('sv_step_topup_amt'),
                    'min_sv_entry_bal'             => $request->input('min_sv_entry_bal'),
                    'max_sv_bal'                   => $request->input('max_sv_bal'),
                    'updated_date'                 => now(),
                    'updated_by'                   => 1,
                ]);
        }

        /* FOR PAPER */
        if($request->input('media_type_id') == 4){

            /* VALIDATIONS */
             $request->validate([
                'media_type_id'  =>'required|integer|min:0',
                'product_type_id'=>'required|integer|min:0',
                'pass_id'        =>'required',
                'pass_name'      =>'required',
                'company_id'     =>'required|integer|min:0',
                'status'         =>'required|boolean',
                'fare_table_id'  =>'required|integer|min:0',
                'start_date'     =>'required',
            ]);


            /* UPDATE QUERY */
            DB::table('pass_inventory')
                ->where('pass_inv_id','=',$id)
                ->update([
                    'media_type_id'                => $request->input('media_type_id'),
                    'card_type_id'                 => null,
                    'product_id'                   => $request->input('product_type_id'),
                    'pass_id'                      => $request->input('pass_id'),
                    'pass_name'                    => $request->input('pass_name'),
                    'description'                  => $request->input('description'),
                    'company_id'                   => $request->input('company_id'),
                    'status'                       => $request->input('status'),
                    'fare_table_id'                => $request->input('fare_table_id'),
                    'start_date'                   => $request->input('start_date'),
                    'end_date'                     => $request->input('end_date'),
                    'same_stn_over_time_limit'     => $request->input('same_stn_over_time_limit'),
                    'same_stn_over_time_pen'       => $request->input('same_stn_over_time_pen'),
                    'same_stn_over_time_max_pen'   => $request->input('same_stn_over_time_max_pen'),
                    'other_stn_over_time_limit'    => $request->input('other_stn_over_time_limit'),
                    'other_stn_over_time_pen'      => $request->input('other_stn_over_time_pen'),
                    'other_stn_over_time_max_pen'  => $request->input('other_stn_over_time_max_pen'),
                    'over_travel_pen'              => $request->input('over_travel_pen'),
                    'entry_mismatch_limit'         => $request->input('entry_mismatch_limit'),
                    'entry_mismatch_same_time_pen' => $request->input('entry_mismatch_same_time_pen'),
                    'entry_mismatch_no_exit_pen'   => $request->input('entry_mismatch_no_exit_pen'),
                    'exit_mismatch_pen'            => $request->input('exit_mismatch_pen'),
                    'entry_exit_control'           => $request->input('entry_exit_control'),
                    'is_test'                      => $request->input('is_test'),
                    'entry_validity_period'        => $request->input('entry_validity_period'),
                    'return_validity_period'       => $request->input('return_validity_period'),
                    'pass_validity_period'         => $request->input('pass_validity_period'),
                    'grace_period'                 => $request->input('grace_period'),
                    'trip_count'                   => $request->input('trip_count'),
                    'daily_trip_limit'             => $request->input('daily_trip_limit'),
                    'issue_permit'                 => $request->input('issue_permit'),
                    'reload_permit'                => $request->input('reload_permit'),
                    'refund_permit'                => $request->input('refund_permit'),
                    'refund_charges'               => $request->input('refund_charges'),
                    'min_sv_topup_amt'             => $request->input('min_sv_topup_amt'),
                    'sv_step_topup_amt'            => $request->input('sv_step_topup_amt'),
                    'min_sv_entry_bal'             => $request->input('min_sv_entry_bal'),
                    'max_sv_bal'                   => $request->input('max_sv_bal'),
                    'updated_date'                 => now(),
                    'updated_by'                   => 1,
                ]);
        }

        /* FOR WHATSAPP */
        if($request->input('media_type_id') == 5){

            /* VALIDATIONS */
             $request->validate([
                'media_type_id'  =>'required|integer|min:0',
                'product_type_id'=>'required|integer|min:0',
                'pass_id'        =>'required',
                'pass_name'      =>'required',
                'company_id'     =>'required|integer|min:0',
                'status'         =>'required|boolean',
                'fare_table_id'  =>'required|integer|min:0',
                'start_date'     =>'required',
            ]);


            /* UPDATE QUERY */
            DB::table('pass_inventory')
                ->where('pass_inv_id','=',$id)
                ->update([
                    'media_type_id'                => $request->input('media_type_id'),
                    'card_type_id'                 => null,
                    'product_id'                   => $request->input('product_type_id'),
                    'pass_id'                      => $request->input('pass_id'),
                    'pass_name'                    => $request->input('pass_name'),
                    'description'                  => $request->input('description'),
                    'company_id'                   => $request->input('company_id'),
                    'status'                       => $request->input('status'),
                    'fare_table_id'                => $request->input('fare_table_id'),
                    'start_date'                   => $request->input('start_date'),
                    'end_date'                     => $request->input('end_date'),
                    'same_stn_over_time_limit'     => $request->input('same_stn_over_time_limit'),
                    'same_stn_over_time_pen'       => $request->input('same_stn_over_time_pen'),
                    'same_stn_over_time_max_pen'   => $request->input('same_stn_over_time_max_pen'),
                    'other_stn_over_time_limit'    => $request->input('other_stn_over_time_limit'),
                    'other_stn_over_time_pen'      => $request->input('other_stn_over_time_pen'),
                    'other_stn_over_time_max_pen'  => $request->input('other_stn_over_time_max_pen'),
                    'over_travel_pen'              => $request->input('over_travel_pen'),
                    'entry_mismatch_limit'         => $request->input('entry_mismatch_limit'),
                    'entry_mismatch_same_time_pen' => $request->input('entry_mismatch_same_time_pen'),
                    'entry_mismatch_no_exit_pen'   => $request->input('entry_mismatch_no_exit_pen'),
                    'exit_mismatch_pen'            => $request->input('exit_mismatch_pen'),
                    'entry_exit_control'           => $request->input('entry_exit_control'),
                    'is_test'                      => $request->input('is_test'),
                    'entry_validity_period'        => $request->input('entry_validity_period'),
                    'return_validity_period'       => $request->input('return_validity_period'),
                    'pass_validity_period'         => $request->input('pass_validity_period'),
                    'grace_period'                 => $request->input('grace_period'),
                    'trip_count'                   => $request->input('trip_count'),
                    'daily_trip_limit'             => $request->input('daily_trip_limit'),
                    'issue_permit'                 => $request->input('issue_permit'),
                    'reload_permit'                => $request->input('reload_permit'),
                    'refund_permit'                => $request->input('refund_permit'),
                    'refund_charges'               => $request->input('refund_charges'),
                    'min_sv_topup_amt'             => $request->input('min_sv_topup_amt'),
                    'sv_step_topup_amt'            => $request->input('sv_step_topup_amt'),
                    'min_sv_entry_bal'             => $request->input('min_sv_entry_bal'),
                    'max_sv_bal'                   => $request->input('max_sv_bal'),
                    'updated_date'                 => now(),
                    'updated_by'                   => 1,
                ]);
        }


        return redirect('pass')->with([
            'status' => true,
            'message' => $request->input('pass_name') . ' PASS UPDATED SUCCESSFULLY.'
        ]);

    }

    /* INDEX VIEW OF PARAMS */
    function params()
    {

        $columns    = Schema::getColumnListing("ms_pass_parameters");
        $Parameters = DB::table('ms_pass_parameters')->get();

        $Params = null;
        for($i = 0; $i <= 3; $i++) {
            for($j = 0; $j < $Parameters->count(); $j++) {
                $Params[$i][$j] = 0;
            }
        }
        return Inertia::render('Pass/Parameters', [
            'Parameters' =>$Parameters,
            'columns'    =>$columns,
            'Params'     =>$Params,
        ])->with([
            'status' => true,
            'message' => ' PARAMETERS UPDATED SUCCESSFULLY.'
        ]);

    }

    /* UPDATE PARAMS */
    function updateParams(Request $request)
    {
        DB::table('ms_pass_parameters')
            ->where('id','=',$request->input('id'))
            ->update
            ([
                'sjt' => 1,
            ]);

        return redirect('pass/parameters')->with([
            'message' => 'PARAMETERS UPDATED SUCCESSFULLY.'
        ]);
    }

    /*API FOR PASS PARAMETERS*/
    public function getParamsForPass(Request $request)
    {
        $pass_type_name = DB::table('ms_product_type')
            ->where('product_type_id', '=', $request->input('product_type_id'))
            ->first('product_name')->product_name;

        return json_encode([
            'disabled'  => DB::table('ms_pass_parameters')->where($pass_type_name, '=', 0)->get('params'),
            'enabled'   => DB::table('ms_pass_parameters')->where($pass_type_name, '=', 1)->get('params')
        ]);

    }

}
