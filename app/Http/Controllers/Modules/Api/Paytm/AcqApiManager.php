<?php

namespace App\Http\Controllers\Modules\Api\Paytm;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Mockery\Exception;


class AcqApiManager extends Controller
{
    /*** -------------------------------------------------- DEBUG -------------------------------------------------- ***/

    function verifyTerminal(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/verifyTerminal", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return $e->getMessage();
        }
    }

    function moneyLoad(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/moneyLoad", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function sale(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/sale", [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function balanceUpdate(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/balanceUpdate", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function voidTrans(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/void", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function serviceCreation(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/serviceCreation", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function updateReceiptAndRevertLastTxn(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/updateReceiptAndRevertLastTxn", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    /*** -------------------------------------------------- PROD ------------------------------------------------- ***/

    /*function verifyTerminal(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/verifyTerminal", [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function moneyLoad(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/moneyLoad", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function sale(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/sale", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function balanceUpdate(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/balanceUpdate", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function voidTrans(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/void", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function serviceCreation(Request $request)
    {

        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/serviceCreation", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }

    function updateReceiptAndRevertLastTxn(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/updateReceiptAndRevertLastTxn", [
                 'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => true,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
           return $e->getMessage();
        }
    }*/

}
