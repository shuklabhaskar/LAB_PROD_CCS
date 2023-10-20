<?php

namespace App\Http\Controllers\Modules\Firmware;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FirmwareController extends Controller
{
    /* FOR SHOWING INDEX PAGE */
    public function index()
    {
        return Inertia::render('Firmware/Index');
    }

    /* UPLOADING FIRMWARE */
    /* UPLOADING FIRMWARE */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'firmware_id' => 'required|integer',
            'description' => 'required',
            'file' => 'required|file|max:100000',
        ]);

        $eqTypeId = $request->input('firmware_id');

        foreach ($request->file() as $files) {

            $fileName = $files->getClientOriginalName();
            $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $fileName);
            $string = $withoutExt;
            $parts = explode('_', $string);
            $fileVersion = $parts[1];
            $folderName = $this->getFolderName($eqTypeId);
            $subFolderName = $this->getFolderName($eqTypeId) . "_" . $fileVersion;
            $files->storeAs('uploads//'.$folderName.'//' . $subFolderName, $fileName, 'public');
            $filePath = "uploads//$folderName//$subFolderName//$fileName";

            DB::table('firmware_upload')->insert([
            'eq_type_id' => $eqTypeId,
                'description' => $request->input('description'),
                'firmware_version' => $fileVersion,
                'firmware_path' => 'app//public//' . $filePath,
            ]);

        }

        return redirect()
            ->to('firmware')
            ->with([
                'message' => 'FIRMWARE UPLOADED SUCCESSFULLY.'
            ]);
    }

    private function getFolderName($eqTypeId)
    {
        $folderNames = [
            1 => 'AG',
            2 => 'TOM',
            6 => 'EDC',
        ];

        return $folderNames[$eqTypeId] ?? 'UNKNOWN';
    }

}
