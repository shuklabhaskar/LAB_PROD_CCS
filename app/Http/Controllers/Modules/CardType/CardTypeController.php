<?php

namespace App\Http\Controllers\Modules\CardType;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CardTypeController extends Controller
{

    /*FOR SHOWING MAIN PAGE WITH DEFAULT DATA*/
    public function index(){

        $cardTypes  = DB::table('card_type')
            ->orderBy('card_type_id','ASC')
            ->get();

        return Inertia::render('CardType/Index', [
            'cardTypes' => $cardTypes
        ]);
    }

    /*FOR CREATING NEW CARD TYPE*/
    public function create(){

        $MediaTypes = DB::table('ms_media_types')->get();
        $PsTypes    = DB::table('ms_payment_scheme')->get();

        return Inertia::render('CardType/Create', [
            'MediaTypes'    => $MediaTypes,
            'PsTypes'       => $PsTypes
        ]);

    }

    /*STORING DATA OF CARD TYPE*/
    public function store(Request $request){

        $request->validate([
            'card_name'              => 'required',
            'media_type_id'          => 'required',
            'card_pro_id'            => 'required|unique:card_type',
            'card_fee'               => 'required',
            'card_sec'               => 'required',
            'status'                 => 'required',
            'start_date'             => 'required',
        ]);

        /*IN CASE OF OL CARDS */
        if ($request->input('media_type_id') == 1 ){

            $request->validate([
                'card_name'              => 'required',
                'media_type_id'          => 'required',
                'card_bin_number'        => 'required|integer',
                'ps_type_id'             => 'required',
                'card_pro_id'            => 'required',
                'card_fee'               => 'required',
                'card_sec'               => 'required',
                'status'                 => 'required',
                'start_date'             => 'required',
            ]);

            $newId  = DB::table('card_type')->orderBy('card_type_id', 'desc')->first('card_type_id');
            $cardID = ($newId != null) ? $newId->card_type_id + 1 : 1;

            DB::table('card_type')->insert([
                'card_id'                   => $cardID,
                'media_type_id'             => $request->input('media_type_id'),
                'card_bin'                  => $request->input('card_bin'),
                'ps_type_id'                => $request->input('ps_type_id'),
                'card_name'                 => $request->input('card_name'),
                'description'               => $request->input('description'),
                'card_pro_id'               => $request->input('card_pro_id'),
                'card_fee'                  => $request->input('card_fee'),
                'card_sec'                  => $request->input('card_sec'),
                'card_sec_refund_permit'    => $request->input('card_sec_refund_permit'),
                'card_sec_refund_charges'   => $request->input('card_sec_refund_charges'),
                'sv_pass_id'                => $request->input('sv_pass_id'),
                'tp_pass_id'                => $request->input('tp_pass_id'),
                'status'                    => $request->input('status'),
                'start_date'                => $request->input('start_date'),
                'end_date'                  => $request->input('end_date'),
                'created_date'              => Carbon::now(),
            ]);
        }

        /* IN CASE OF CL CARDS */
        if ($request->input('media_type_id') == 2 ){

            $request->validate([
                'card_name'              => 'required',
                'media_type_id'          => 'required',
                'ps_type_id'             => 'required',
                'card_pro_id'            => 'required',
                'card_fee'               => 'required',
                'card_sec'               => 'required',
                'status'                 => 'required',
                'start_date'             => 'required',
            ]);

            $newId  = DB::table('card_type')->orderBy('card_type_id', 'desc')->first('card_type_id');
            $cardID = ($newId != null) ? $newId->card_type_id + 1 : 1;

            DB::table('card_type')->insert([
                'card_id'                   => $cardID,
                'media_type_id'             => $request->input('media_type_id'),
                'ps_type_id'                => $request->input('ps_type_id'),
                'card_name'                 => $request->input('card_name'),
                'description'               => $request->input('description'),
                'card_pro_id'               => $request->input('card_pro_id'),
                'card_fee'                  => $request->input('card_fee'),
                'card_sec'                  => $request->input('card_sec'),
                'card_sec_refund_permit'    => $request->input('card_sec_refund_permit'),
                'card_sec_refund_charges'   => $request->input('card_sec_refund_charges'),
                'sv_pass_id'                => $request->input('sv_pass_id'),
                'tp_pass_id'                => $request->input('tp_pass_id'),
                'status'                    => $request->input('status'),
                'start_date'                => $request->input('start_date'),
                'end_date'                  => $request->input('end_date'),
                'created_date'              => Carbon::now(),
            ]);
        }

        /* FOR PQR AND WHATSAPP*/
        if ($request->input('media_type_id') == 3 || $request->input('media_type_id') == 4 || $request->input('media_type_id') == 5) {

            $newId  = DB::table('card_type')->orderBy('card_type_id', 'desc')->first('card_type_id');
            $cardID = ($newId != null) ? $newId->card_type_id + 1 : 1;

            $request->validate([
                'card_name'              => 'required',
                'media_type_id'          => 'required',
                'card_pro_id'            => 'required',
                'card_fee'               => 'required',
                'card_sec'               => 'required',
                'status'                 => 'required',
                'start_date'             => 'required',
            ]);

            DB::table('card_type')->insert([
                'card_id'                   => $cardID,
                'media_type_id'             => $request->input('media_type_id'),
                'card_name'                 => $request->input('card_name'),
                'description'               => $request->input('description'),
                'card_pro_id'               => $request->input('card_pro_id'),
                'card_fee'                  => $request->input('card_fee'),
                'card_sec'                  => $request->input('card_sec'),
                'card_sec_refund_permit'    => $request->input('card_sec_refund_permit'),
                'card_sec_refund_charges'   => $request->input('card_sec_refund_charges'),
                'sv_pass_id'                => $request->input('sv_pass_id'),
                'tp_pass_id'                => $request->input('tp_pass_id'),
                'status'                    => $request->input('status'),
                'start_date'                => $request->input('start_date'),
                'end_date'                  => $request->input('end_date'),
                'created_date'              => Carbon::now(),
            ]);

        }

        return redirect('cardType')->with([
            'status'    => true,
            'message'   => $request->input('card_name') .' CARD TYPE CREATED SUCCESSFULLY.'
        ]);
    }

    /* EDIT SELECTED CARD TYPE */
    public function edit($id){

        $cardType = DB::table('card_type as ct')
            ->where('card_type_id','=',$id)
            ->join('ms_media_types as mt','mt.media_type_id','=','ct.media_type_id')
            ->join('ms_payment_scheme as ps','ps.ps_type_id','=','ct.ps_type_id')
            ->select([
                'ct.card_type_id',
                'ct.card_id',
                'ct.card_name',
                'ct.card_bin',
                'ct.description',
                'ct.card_pro_id',
                'ct.card_fee',
                'ct.card_sec',
                'ct.card_sec_refund_permit',
                'ct.card_sec_refund_charges',
                'ct.sv_pass_id',
                'ct.tp_pass_id',
                'ct.status',
                'ct.start_date',
                'ct.end_date',
                'ct.created_date',
                'ct.updated_by',
                'mt.media_type_id',
                'mt.media_name',
                'ps.ps_type_id',
                'ps.ps_name'
            ])
            ->first();

        $MediaTypes  = DB::table('ms_media_types')->get();
        $PsTypes     = DB::table('ms_payment_scheme')->get();

        return Inertia::render('CardType/Edit', [
            'cardType'   => $cardType,
            'MediaTypes' => $MediaTypes,
            'PsTypes'    => $PsTypes,
        ]);
    }

    /* UPDATE SELECTED CARD TYPE */
    public function update(Request $request,$id){

        if ($request->input('media_type_id') == 1 ) {

            $request->validate([
                'card_name'         => 'required',
                'media_type_id'     => 'required',
                'card_bin_number'   => 'required|integer',
                'ps_type_id'        => 'required',
                'card_pro_id'       => 'required',
                'card_fee'          => 'required',
                'card_sec'          => 'required',
                'status'            => 'required',
                'start_date'        => 'required',
            ]);

            DB::table('card_type')->where('card_type_id','=',$id)->update([
                'media_type_id'           => $request->input('media_type_id'),
                'ps_type_id'              => $request->input('ps_type_id'),
                'card_bin'                => $request->input('card_bin'),
                'card_name'               => $request->input('card_name'),
                'description'             => $request->input('description'),
                'card_pro_id'             => $request->input('card_pro_id'),
                'card_fee'                => $request->input('card_fee'),
                'card_sec'                => $request->input('card_sec'),
                'card_sec_refund_permit'  => $request->input('card_sec_refund_permit'),
                'card_sec_refund_charges' => $request->input('card_sec_refund_charges'),
                'sv_pass_id'              => $request->input('sv_pass_id'),
                'tp_pass_id'              => $request->input('tp_pass_id'),
                'status'                  => $request->input('status'),
                'start_date'              => $request->input('start_date'),
                'end_date'                => $request->input('end_date'),
                'created_date'            => Carbon::now(),
                'updated_by'              => 1,
            ]);

        }

        if ($request->input('media_type_id') == 2){

            $request->validate([
                'card_name'      => 'required',
                'media_type_id'  => 'required',
                'ps_type_id'     => 'required',
                'card_pro_id'    => 'required',
                'card_fee'       => 'required',
                'card_sec'       => 'required',
                'status'         => 'required',
                'start_date'     => 'required',
            ]);

            DB::table('card_type')->where('card_type_id','=',$id)->update([
                'media_type_id'           => $request->input('media_type_id'),
                'ps_type_id'              => $request->input('ps_type_id'),
                'card_name'               => $request->input('card_name'),
                'description'             => $request->input('description'),
                'card_pro_id'             => $request->input('card_pro_id'),
                'card_fee'                => $request->input('card_fee'),
                'card_sec'                => $request->input('card_sec'),
                'card_sec_refund_permit'  => $request->input('card_sec_refund_permit'),
                'card_sec_refund_charges' => $request->input('card_sec_refund_charges'),
                'sv_pass_id'              => $request->input('sv_pass_id'),
                'tp_pass_id'              => $request->input('tp_pass_id'),
                'status'                  => $request->input('status'),
                'start_date'              => $request->input('start_date'),
                'end_date'                => $request->input('end_date'),
                'created_date'            => Carbon::now(),
                'updated_by'              => 1,
            ]);

        }

        DB::table('card_type')->where('card_type_id','=',$id)->update([
            'media_type_id'           => $request->input('media_type_id'),
            'card_name'               => $request->input('card_name'),
            'description'             => $request->input('description'),
            'card_pro_id'             => $request->input('card_pro_id'),
            'card_fee'                => $request->input('card_fee'),
            'card_sec'                => $request->input('card_sec'),
            'card_sec_refund_permit'  => $request->input('card_sec_refund_permit'),
            'card_sec_refund_charges' => $request->input('card_sec_refund_charges'),
            'sv_pass_id'              => $request->input('sv_pass_id'),
            'tp_pass_id'              => $request->input('tp_pass_id'),
            'status'                  => $request->input('status'),
            'start_date'              => $request->input('start_date'),
            'end_date'                => $request->input('end_date'),
            'created_date'            => Carbon::now(),
            'updated_by'              => 1,
        ]);

        return redirect('cardType')->with([
            'status'  => true,
            'message' => $request->input('card_name') .' CARD TYPE UPDATED SUCCESSFULLY.'
        ]);

    }

}
