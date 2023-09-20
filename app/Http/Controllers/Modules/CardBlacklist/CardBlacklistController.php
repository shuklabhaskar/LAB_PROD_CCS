<?php

namespace App\Http\Controllers\Modules\CardBlacklist;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardBlacklistController extends Controller
{
    public function index()
    {
        $MediaTypes         = DB::table('ms_media_types')->where('media_type_id', '=', 2)->get();
        $BlacklistReasons   = DB::table('ms_cl_blacklist_reason')->get();

        $blacklistedCards   = DB::table('cl_blacklist')
            ->join('ms_cl_blacklist_reason', 'ms_cl_blacklist_reason.ms_blk_reason_id', '=', 'cl_blacklist.ms_blk_reason_id')
            ->orderBy('cl_blk_id','ASC')
            ->get();

        return Inertia::render('CardBlacklist/Index', [
            'MediaTypes'        => $MediaTypes,
            'BlacklistReasons'  => $BlacklistReasons,
            'blacklistedCards'  => $blacklistedCards
        ]);

    }

    public function store(Request $request)
    {
            $request->validate([
                'engraved_id'       => 'required|unique:cl_blacklist|exists:cl_sn_mapping',
                'media_type_id'     => 'required',
                'ms_blk_reason_id'  => 'required',
            ],

            [
                'engraved_id.required'          => 'Please Enter Start Serial Number.',
                'engraved_id.unique'            => 'This Serial Number is Already Blacklisted.',
                'engraved_id.WithoutWhitespace' => 'Enter Serial Number Without Space.',
                'ms_blk_reason_id.required'     => 'Please Select Blacklist Reason.',
                'media_type_id.required'        => 'Please Select Media Type.'
            ]);

        if ($request->input('end_serial') == null){


            $searchChipSN  = DB::table('cl_sn_mapping')
                ->where('engraved_id','=',$request->input('engraved_id'))
                ->first('chip_id');


            DB::table('cl_blacklist')->insert([
                'ms_blk_reason_id'  => $request->input('ms_blk_reason_id'),
                'start_date'        => now(),
                'engraved_id'       => $request->input('engraved_id'),
                'chip_id'           => $searchChipSN->chip_id,
            ]);

        }

        if ($request->input('end_serial') != null) {

            $engravedIds = range($request->input('engraved_id'), $request->input('end_serial'));

            foreach ($engravedIds as $engravedId) {

                $searchChipSN = DB::table('cl_sn_mapping')
                    ->where('engraved_id', $engravedId)
                    ->first('chip_id');

                DB::table('cl_blacklist')->insert([
                    'ms_blk_reason_id'  => $request->input('ms_blk_reason_id'),
                    'start_date'        => now(),
                    'engraved_id'       => $engravedId,
                    'chip_id'           => $searchChipSN->chip_id,
                ]);

            }

        }

        return redirect('/card/blacklist')->with([
            'status'  => true,
            'message' => 'CARD BLACKLIST UPDATED SUCCESSFULLY.'
        ]);

    }

    /* DELETE CARD BLACKLIST */
    public function delete($id){

        DB::table('cl_blacklist')
            ->where('cl_blk_id', $id)
            ->delete();

        return redirect('/card/blacklist')->with([
            'status'  => true,
            'message' => 'CARD BLACKLIST UPDATED SUCCESSFULLY.'
        ]);

    }

}
