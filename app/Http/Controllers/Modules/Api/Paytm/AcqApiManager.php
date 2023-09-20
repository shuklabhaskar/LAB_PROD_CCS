<?php

namespace App\Http\Controllers\Modules\Api\Paytm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class AcqApiManager extends Controller
{
    // -------------------------------------------------- DEBUG --------------------------------------------------

    
    // function verifyTerminal(Request $request): \Illuminate\Support\Collection
    // {
    //     $body = $request->input('body');
    //     return Http::timeout(3*60)->retry(3)
    //         ->withBody(json_encode($body), 'application/json')
    //         ->post("https://nos-staging.paytm.com/nos/verifyTerminal")
    //         ->collect();
    // }

    // function moneyLoad(Request $request): \Illuminate\Support\Collection
    // {
    //     $body = $request->input('body');
    //     return Http::timeout(3*60)->retry(3)
    //         ->withBody(json_encode($body), 'application/json')
    //         ->post("https://nos-staging.paytm.com/nos/moneyLoad")
    //         ->collect();
    // }

    // function sale(Request $request): \Illuminate\Support\Collection
    // {
    //     $body = $request->input('body');
    //     return Http::timeout(3*60)->retry(3)
    //         ->withBody(json_encode($body), 'application/json')
    //         ->post("https://nos-staging.paytm.com/nos/sale")
    //         ->collect();
    // }

    // function balanceUpdate(Request $request): \Illuminate\Support\Collection
    // {
    //     $body = $request->input('body');
    //     return Http::timeout(3*60)->retry(3)
    //         ->withBody(json_encode($body), 'application/json')
    //         ->post("https://nos-staging.paytm.com/nos/balanceUpdate")
    //         ->collect();
    // }

    // function voidTrans(Request $request): \Illuminate\Support\Collection
    // {
    //     $body = $request->input('body');
    //     return Http::timeout(3*60)->retry(3)
    //         ->withBody(json_encode($body), 'application/json')
    //         ->post("https://nos-staging.paytm.com/nos/void")
    //         ->collect();
    // }

    // function serviceCreation(Request $request): \Illuminate\Support\Collection
    // {
    //     $body = $request->input('body');
    //     return Http::timeout(3*60)->retry(3)
    //         ->withBody(json_encode($body), 'application/json')
    //         ->post("https://nos-staging.paytm.com/nos/serviceCreation")
    //         ->collect();
    // }

    // function updateReceiptAndRevertLastTxn(Request $request): \Illuminate\Support\Collection
    // {
    //     $body = $request->input('body');
    //     return Http::timeout(3*60)->retry(3)
    //         ->withBody(json_encode($body), 'application/json')
    //         ->post("https://nos-staging.paytm.com/nos/updateReceiptAndRevertLastTxn")
    //         ->collect();
    // }
    

    
    // -------------------------------------------------- PROD -------------------------------------------------
    function verifyTerminal(Request $request): \Illuminate\Support\Collection
    {
        $body = $request->input('body');
        return Http::timeout(3*60)->retry(3)
            ->withBody(json_encode($body), 'application/json')
            ->post("https://securegw.paytm.in/nos/verifyTerminal")
            ->collect();
    }

    function moneyLoad(Request $request): \Illuminate\Support\Collection
    {
        $body = $request->input('body');
        return Http::timeout(3*60)->retry(3)
            ->withBody(json_encode($body), 'application/json')
            ->post("https://securegw.paytm.in/nos/moneyLoad")
            ->collect();
    }

    function sale(Request $request): \Illuminate\Support\Collection
    {
        $body = $request->input('body');
        return Http::timeout(3*60)->retry(3)
            ->withBody(json_encode($body), 'application/json')
            ->post("https://securegw.paytm.in/nos/sale")
            ->collect();
    }

    function balanceUpdate(Request $request): \Illuminate\Support\Collection
    {
        $body = $request->input('body');
        return Http::timeout(3*60)->retry(3)
            ->withBody(json_encode($body), 'application/json')
            ->post("https://securegw.paytm.in/nos/balanceUpdate")
            ->collect();
    }

    function voidTrans(Request $request): \Illuminate\Support\Collection
    {
        $body = $request->input('body');
        return Http::timeout(3*60)->retry(3)
            ->withBody(json_encode($body), 'application/json')
            ->post("https://securegw.paytm.in/nos/void")
            ->collect();
    }

    function serviceCreation(Request $request): \Illuminate\Support\Collection
    {
        $body = $request->input('body');
        return Http::timeout(3*60)->retry(3)
            ->withBody(json_encode($body), 'application/json')
            ->post("https://securegw.paytm.in/nos/serviceCreation")
            ->collect();
    }

    function updateReceiptAndRevertLastTxn(Request $request): \Illuminate\Support\Collection
    {
        $body = $request->input('body');
        return Http::timeout(3*60)->retry(3)
            ->withBody(json_encode($body), 'application/json')
            ->post("https://securegw.paytm.in/nos/updateReceiptAndRevertLastTxn")
            ->collect();
    } 

}
