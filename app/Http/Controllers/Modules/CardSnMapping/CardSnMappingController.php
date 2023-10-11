<?php

namespace App\Http\Controllers\Modules\CardSnMapping;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CardSnMappingController extends Controller
{ public function index()
{
    return Inertia::render('CardSnMapping/Index');
}

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv',
        ]);

        $uploadedFile = $request->file('file');

        $destinationPath = 'ClSnMapping';

        $excelFileName = uniqid('cl_sn_mapping');

        // Create the directory in the storage/app directory
        Storage::makeDirectory($destinationPath);

        $excelFilePath = $uploadedFile->storeAs($destinationPath, $excelFileName);

        $spreadsheet = IOFactory::load(storage_path("app/$excelFilePath"));
        $worksheet = $spreadsheet->getActiveSheet();
        $firstRow = true;
        $insertedCount = 0;

        foreach ($worksheet->getRowIterator() as $row) {

            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            $cellIterator = $row->getCellIterator();
            $cellIterator->seek('A');
            $engraved_id = $cellIterator->current()->getValue();

            $cellIterator->seek('B');
            $hexadecimalValue = $cellIterator->current()->getValue();

            /* CONVERTING FROM HEXADECIMAL TO DECIMAL */
            $decimalValue = hexdec($hexadecimalValue);

            if (!is_null($engraved_id)) {
                try {
                    $existingRecord = DB::table('cl_sn_mapping')
                        ->where('chip_id', '=', $decimalValue)
                        ->first();

                    if (is_null($existingRecord)) {
                        /* INSERT RECORD ONLY IF IT IS NOT IN TABLE */
                        DB::table('cl_sn_mapping')->insert([
                            'engraved_id' => $engraved_id,
                            'chip_id' => $decimalValue,
                        ]);

                        $insertedCount++;
                    }
                } catch (QueryException $e) {
                    continue;
                }
            }
        }

        return redirect()
            ->to('/card/sn/mapping')
            ->with([
                'message' => $insertedCount . ' CARD MAPPING DONE SUCCESSFULLY.'
            ]);
    }
}
