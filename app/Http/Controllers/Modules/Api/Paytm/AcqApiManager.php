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
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function moneyLoad(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/moneyLoad", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function sale(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/sale", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function balanceUpdate(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/balanceUpdate", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function voidTrans(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/void", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function serviceCreation(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/serviceCreation", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function updateReceiptAndRevertLastTxn(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://nos-staging.paytm.com/nos/updateReceiptAndRevertLastTxn", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    /*** -------------------------------------------------- PROD ------------------------------------------------- ***/

    /*function verifyTerminal(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/verifyTerminal", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function moneyLoad(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/moneyLoad", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function sale(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/sale", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function balanceUpdate(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/balanceUpdate", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function voidTrans(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/void", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function serviceCreation(Request $request)
    {

        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/serviceCreation", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }

    function updateReceiptAndRevertLastTxn(Request $request)
    {
        try {
            $body = $request->input('body');
            $client = new Client();
            $response = $client->post("https://securegw.paytm.in/nos/updateReceiptAndRevertLastTxn", [
                'body' => json_encode($body),
                'timeout' => 3 * 60,
                'http_errors' => false,
            ]);
            return json_decode($response->getBody()->getContents());
        } catch (Exception|GuzzleException $e) {
            return Response::json([
                'body' => [
                    'resultCode' => 101,
                    'resultMsg' => $e->getMessage()
                ]
            ]);
        }
    }*/

}
