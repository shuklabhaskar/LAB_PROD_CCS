<?php

namespace App\Http\Controllers\Modules\CardSnMapping;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Illuminate\Http\Request;


class CardSnMappingController extends Controller
{
    public function index()
    {

        return Inertia::render('CardSnMapping/Index', [

        ]);

    }

    public function store(Request $request)
    {
                //Move Uploaded File to public folder
                $destinationPath = 'ClSnSheet';
                $ClSnSheet = $request->file->getClientOriginalName();
                $request->file->move(public_path($destinationPath), $ClSnSheet);

                dd($ClSnSheet);

    }
}






