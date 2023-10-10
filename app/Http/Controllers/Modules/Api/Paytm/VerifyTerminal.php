<?php /** @noinspection LaravelFunctionsInspection */

namespace App\Http\Controllers\Modules\Api\Paytm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VerifyTerminal extends Controller
{
    public function verify(Request $request)
    {

        $request->getContent();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://securegw.paytm.in/nos/verifyTerminal',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "body": {
                    "serialNo": "18058A2A",
                    "stan": "000001",
                    "tid": "10875729",
                    "vendorName": "Feig"
                },
                "head": {
                    "clientId": "FIEG0N001",
                    "isP2PEDevice": true,
                    "mac": "95381FF81E5DD885",
                    "macKsn": "FFFF0303040004A00005",
                    "version": "1.0"
                }
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;
    }

}
