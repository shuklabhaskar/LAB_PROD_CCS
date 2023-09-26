<?php

namespace App\Http\Controllers\Modules\CardSnMapping;

use App\Http\Controllers\Controller;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CardSnMappingController extends Controller
{
    public function index()
    {
        return Inertia::render('CardSnMapping/Index');
    }

    /**
     * @throws Exception
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv',
        ]);

        $uploadedFile = $request->file('file');

        $destinationPath = 'ClSnMapping';

        $excelFileName = uniqid('cl_sn_mapping');

        $clSnSheetPath = public_path($destinationPath);

        if (file_exists($clSnSheetPath)) {
            File::deleteDirectory($clSnSheetPath);
        }

        File::makeDirectory($clSnSheetPath);

        $uploadedFile->move($clSnSheetPath, $excelFileName);

        $excelFilePath = $clSnSheetPath . '/' . $excelFileName;
        $spreadsheet = IOFactory::load($excelFilePath);
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
                $existingRecord = DB::table('cl_sn_mapping')
                    ->where('engraved_id', '=', $engraved_id)
                    ->first();

                if (is_null($existingRecord)) {

                    DB::table('cl_sn_mapping')->insert([
                        'engraved_id' => $engraved_id,
                        'chip_id' => $decimalValue,
                    ]);

                    $insertedCount++;

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



















































































































































































































































































































































































































































































































































































































