<?php

namespace App\Http\Controllers\Modules\Api\CL;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDOException;

class ClCardReplacement extends Controller
{
    public function getCardData($engravedId)
    {
        if ($engravedId == null) {
            return response([
                'status' => false,
                'error' => "please Provide engravedId !"
            ]);
        }

        try {

            $cardData = DB::table('cl_status')
                ->where('engraved_id', '=', $engravedId)
                ->first();

            if ($cardData == null) {

                return response([
                    'status' => false,
                    'error' => "Card Data Does Not Exists"
                ]);

            } else {

                $cardBlacklisted = DB::table('cl_blacklist')
                    ->where('engraved_id', '=', $engravedId)
                    ->value('engraved_id');

                if ($cardBlacklisted) {
                    return response([
                        'status' => false,
                        'error' => "This Card is Blacklisted"
                    ]);
                } else {

                    return response([
                        'status' => true,
                        'data' => $cardData
                    ]);
                }
            }

        } catch (PDOException $e) {
            return response([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }


}
