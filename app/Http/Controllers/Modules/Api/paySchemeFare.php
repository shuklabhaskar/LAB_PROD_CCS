<?php

namespace App\Http\Controllers\Modules\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class paySchemeFare extends Controller
{
    public function payScheme()
    {
        DB::table('ol_sv_validation')
            ->where('pay_scheme', '=', 2)
            ->where('val_type_id', '=', 2)
            ->update([
                'amt_deducted' => 3
            ]);

    }
}
