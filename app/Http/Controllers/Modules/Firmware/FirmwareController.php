<?php

namespace App\Http\Controllers\Modules\Firmware;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FirmwareController extends Controller
{
    /* FOR SHOWING INDEX PAGE */
    public function index()
    {
        return Inertia::render('Firmware/Index');
    }

    /* UPLOADING FIRMWARE */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'firmware_id' => 'required|integer',
        ]);

        /* IF FIRMWARE IS SELECTED FOR AG */
        if ($request->firmware_id == 1){
           $request->validate([
                'firmware_id' => 'required|integer',
                'description' => 'required',
                'file'        => 'required',
            ]);

            foreach ($request->file() as $files){

                $fileName    = $files->getClientOriginalName();
                $withoutExt  = preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName);
                $string      = $withoutExt;
                $parts       = explode('_', $string);
                $fileVersion = $parts[1];

                $files->storeAs('uploads//AG//'.'AG_'.$fileVersion,$fileName,'public');
                $filePath = "uploads//AG//AG_$fileVersion//$fileName";

                DB::table('firmware_upload')->insert([
                    'firmware_id'      =>1,
                    'description'      =>$request->description,
                    'firmware_version' =>$fileVersion,
                    'firmware_path'    =>'app//public//'.$filePath,
                ]);

            }

        }

        /* IF FIRMWARE IS SELECTED FOR TOM */
        if ($request->firmware_id == 2){
             $request->validate([
                'firmware_id' => 'required|integer',
                'description' => 'required',
                'file'        => 'required',
            ]);

            foreach ($request->file() as $files){

                $fileName    = $files->getClientOriginalName();
                $withoutExt  = preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName);
                $string      = $withoutExt;
                $parts       = explode('_', $string);
                $fileVersion = $parts[1];
                $files->storeAs('uploads//TOM//'.'TOM_'.$fileVersion,$fileName,'public');
                $filePath    = "uploads//TOM//TOM_$fileVersion//$fileName";

                DB::table('firmware_upload')->insert([
                    'firmware_id'       =>2,
                    'description'       =>$request->description,
                    'firmware_version'  =>$fileVersion,
                    'firmware_path'     =>'app//public//'.$filePath,
                ]);
            }

        }

        /* IF FIRMWARE IS SELECTED FOR EDC */
        if ($request->firmware_id == 6){

            $request->validate([
                'firmware_id' => 'required|integer',
                'description' => 'required',
                'file'        => 'required',
            ]);

            foreach ($request->file() as $files){

                $fileName    = $files->getClientOriginalName();
                $withoutExt  = preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName);
                $string      = $withoutExt;
                $parts       = explode('_', $string);
                $fileVersion = $parts[1];
                $files->storeAs('uploads//EDC//'.'EDC_'.$fileVersion,$fileName,'public');
                $filePath    = "uploads//EDC//EDC_$fileVersion//$fileName";

                DB::table('firmware_upload')->insert([
                    'firmware_id'       =>6,
                    'description'       =>$request->description,
                    'firmware_version'  =>$fileVersion,
                    'firmware_path'     =>'app//public//'.$filePath,
                ]);
            }


        }

        return redirect()
            ->to('firmware')
            ->with([
                'message' => 'FIRMWARE UPLOADED SUCCESSFULLY.'
            ]);

    }

}
