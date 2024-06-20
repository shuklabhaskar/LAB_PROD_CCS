<?php

namespace App\Http\Controllers\Modules\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OperatorController extends Controller
{
    public function index()
    {
        $Operators = DB::table("ms_operators")->get();

        return Inertia::render('Operator/Index', [
            'operators' => $Operators
        ]);
    }

    public function create(Request $request){


        return Inertia::render('Operator/Create');

    }
}
