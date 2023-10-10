<?php

use App\Http\Controllers\Modules\Api\CardSaleSettlement;
use App\Http\Controllers\Modules\Api\CL\ClAccounting;
use App\Http\Controllers\Modules\Api\CL\ClCardReplacement;
use App\Http\Controllers\Modules\Api\CL\ClIndraCardReplacement;
use App\Http\Controllers\Modules\Api\CL\ClSnMapping;
use App\Http\Controllers\Modules\Api\CL\ClValidation;
use App\Http\Controllers\Modules\Api\ConfigApiController;
use App\Http\Controllers\Modules\Api\CrashReport\crashReports;
use App\Http\Controllers\Modules\Api\Equipment;
use App\Http\Controllers\Modules\Api\Firmware\Firmware;
use App\Http\Controllers\Modules\Api\NewConfigApiController;
use App\Http\Controllers\Modules\Api\OlSettlement;
use App\Http\Controllers\Modules\Api\OlSvAccounting;
use App\Http\Controllers\Modules\Api\paySchemeFare;
use App\Http\Controllers\Modules\Api\Paytm\AcqApiManager;
use App\Http\Controllers\Modules\Api\Paytm\IssuanceApiManager;
use App\Http\Controllers\Modules\Api\Paytm\OlAcqTxn;
use App\Http\Controllers\Modules\Api\Paytm\VerifyTerminal;
use App\Http\Controllers\Modules\Api\SettleOlTransaction;
use App\Http\Controllers\Modules\Api\TidController;
use App\Http\Controllers\Modules\Pass\PassController;
use App\Http\Controllers\Modules\ReportApi\CashCollection;
use App\Http\Controllers\Modules\ReportApi\CL\DailyRidershipReport;
use App\Http\Controllers\Modules\ReportApi\CL\RevenueReport;
use App\Http\Controllers\Modules\ReportApi\DailyRidership;
use App\Http\Controllers\Modules\ReportApi\Revenue;
use App\Http\Controllers\Modules\ReportApi\TravelApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('get/params', [PassController::class, 'getParamsForPass']);

/*DOWNLOAD AND CHECK UPDATE OF FIRMWARE*/
Route::post('/checkUpdate', [Firmware::class, 'checkUpdate']);
Route::get('/getFirmware/{uploadId}', [Firmware::class, 'getFirmware']);

/* GET CONFIGURATION V0 */
Route::post('config', [ConfigApiController::class, 'getConfig']);

/* API CONTROLLER V1 */
Route::post('v1/config',[NewConfigApiController::class,'getConfig']);

/* SETTLE OPEN LOOP TRANSACTION */
Route::post('settleOlTransaction', [SettleOlTransaction::class, 'setOlTransaction']);

/* SETTLE SV ACCOUNTING */
Route::post('syncOlAccTrans', [OlSvAccounting::class, 'OlSvAccounting']);

/* FOR GETTING TERMINAL ID */
Route::post('tidDetails', [TidController::class, 'tidDetails']);

/* EDIT TID DETAILS */
Route::post('editEdcDetails', [TidController::class, 'editEdcDetails']);
Route::post('exitTidDetails', [TidController::class, 'exitTidDetails']);
Route::post('entryTidDetails', [TidController::class, 'entryTidDetails']);

/* FOR SETTLE COMMON CARD SALE */
Route::post('syncOlSaleTrans', [CardSaleSettlement::class, 'cardSale']);

Route::post('eqRole', [Equipment::class, 'eqModeID']);

/* CHECK SCS AVAILABILITY */
Route::post('checkSCS', [Equipment::class, 'checkSCS']);
Route::post('/payScheme', [paySchemeFare::class, 'payScheme']);

/* GET ROLES OF EQUIPMENT */
Route::post('/getRoles', [Equipment::class, 'getRoles']);

/* SETTLEMENT API */
Route::get('/getSettlements', [OlSettlement::class, 'getSettlements']);
Route::post('/setSettlements', [OlSettlement::class, 'setSettlements']);

/* VERIFY TERMINAL */
Route::post('/verifyTerminal', [VerifyTerminal::class, 'verify']);
Route::get('/olAcqTxn', [OlAcqTxn::class, 'index']);

/* PAYTM */
Route::post('/otp/send',[IssuanceApiManager::class,'sendOtp']);
Route::post('/otp/verify',[IssuanceApiManager::class,'verifyOtp']);
Route::post('/kyc/details',[IssuanceApiManager::class,'kycDetail']);
Route::post('/kyc/submit',[IssuanceApiManager::class,'submitKyc']);
Route::post('/card/activation',[IssuanceApiManager::class,'activateCard']);
Route::post('/card/status',[IssuanceApiManager::class,'activationStatus']);

Route::post('/verifyTerminal',[AcqApiManager::class,'verifyTerminal']);
Route::post('/moneyLoad',[AcqApiManager::class,'moneyLoad']);
Route::post('/sale',[AcqApiManager::class,'sale']);
Route::post('/balanceUpdate',[AcqApiManager::class,'balanceUpdate']);
Route::post('/void',[AcqApiManager::class,'voidTrans']);
Route::post('/serviceCreation',[AcqApiManager::class,'serviceCreation']);
Route::post('/updateReceiptAndRevertLastTxn',[AcqApiManager::class,'updateReceiptAndRevertLastTxn']);

/*CRASH REPORTS*/
Route::post('/gateCrashReport', [crashReports::class,'gateLog']);
Route::post('/tomCrashReport', [crashReports::class, 'tomLog']);
Route::post('/edcCrashReport', [crashReports::class, 'edcLog']);

/*CLOSE LOOP API*/
Route::get('/clSnMapping', [ClSnMapping::class, 'index']);

/* SETTLE CL SV & TP ACCOUNTING TRANSACTION */
Route::post('/sync/cl/accounting',[ClAccounting::class,'ClAccounting']);

/* SETTLE CL SV & TP VALIDATION TRANSACTION */
Route::post('/sync/cl/validation', [ClValidation::class, 'setClTransaction']);

/*CL INDRA CARD REP */
Route::post('/cl/indra/card/rep',[ClIndraCardReplacement::class,'store']);

/* GETTING CARD DATA FOR REPLACEMENT */
Route::get('/cl/card/rep/{engravedId}',[ClCardReplacement::class,'getCardData']);



/* REPORT API'S*/
Route::middleware(['basic_auth'])->group(function (){
    /* OPEN LOOP API */
    Route::post('/oldailyRidership',[DailyRidership::class,'index']);
    Route::post('/olcashCollection',[CashCollection::class,'index']);
    Route::post('/olrevenue',[Revenue::class,'index']);
    Route::post('/olValReport',[TravelApiController::class,'getReport']);
    /* MMOPL OL SAP FICO POSTING */
    /* Route::get('/olFicoReport', [OlFicoReport::class, 'index']);*/

    /* CLOSED LOOP API */
    Route::post('/cl/Daily/Ridership',[DailyRidershipReport::class,'dailyRidership']);
    Route::post('/cl/revenue',[RevenueReport::class,'revenue']);

});






