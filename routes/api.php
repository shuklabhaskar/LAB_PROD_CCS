<?php

use App\Http\Controllers\Modules\Api\CardSaleSettlement;
use App\Http\Controllers\Modules\Api\CL\ClAccounting;
use App\Http\Controllers\Modules\Api\CL\ClCardReplacement;
use App\Http\Controllers\Modules\Api\CL\ClIndraCardReplacement;
use App\Http\Controllers\Modules\Api\CL\ClInitialisation;
use App\Http\Controllers\Modules\Api\CL\ClSnMapping;
use App\Http\Controllers\Modules\Api\CL\ClValidation;
use App\Http\Controllers\Modules\Api\ConfigApi\V2ConfigApiController;
use App\Http\Controllers\Modules\Api\ConfigApiController;
use App\Http\Controllers\Modules\Api\Equipment;
use App\Http\Controllers\Modules\Api\Firmware\Firmware;
use App\Http\Controllers\Modules\Api\NewConfigApiController;
use App\Http\Controllers\Modules\Api\OlSettlement;
use App\Http\Controllers\Modules\Api\OlSvAccounting;
use App\Http\Controllers\Modules\Api\paySchemeFare;
use App\Http\Controllers\Modules\Api\Paytm\AcqApiManager;
use App\Http\Controllers\Modules\Api\Paytm\IssuanceApiManager;
use App\Http\Controllers\Modules\Api\Paytm\OlAcqTxn;
use App\Http\Controllers\Modules\Api\Paytm\Settlement\Settlement;
use App\Http\Controllers\Modules\Api\Paytm\VerifyTerminal;
use App\Http\Controllers\Modules\Api\SettleOlTransaction;
use App\Http\Controllers\Modules\Api\TidController;
use App\Http\Controllers\Modules\CardBlacklist\CardBlacklistController;
use App\Http\Controllers\Modules\Pass\PassController;
use App\Http\Controllers\Modules\ReportApi\CashCollection;
use App\Http\Controllers\Modules\ReportApi\CL\AfcAuditApi\StoreValueAuditApi;
use App\Http\Controllers\Modules\ReportApi\CL\AfcAuditApi\TripPassAuditApi;
use App\Http\Controllers\Modules\ReportApi\CL\CardDetail;
use App\Http\Controllers\Modules\ReportApi\CL\ClAccReport;
use App\Http\Controllers\Modules\ReportApi\CL\ClFicoReport;
use App\Http\Controllers\Modules\ReportApi\CL\ClSapReport;
use App\Http\Controllers\Modules\ReportApi\CL\ClTravelApi;
use App\Http\Controllers\Modules\ReportApi\CL\DailyRidershipReport;
use App\Http\Controllers\Modules\ReportApi\CL\RevenueReport;
use App\Http\Controllers\Modules\ReportApi\CL\StoreValueExitRevenue;
use App\Http\Controllers\Modules\ReportApi\CL\TripPassExitRevenue;
use App\Http\Controllers\Modules\ReportApi\DailyRidership;
use App\Http\Controllers\Modules\ReportApi\KnowYourLoad;
use App\Http\Controllers\Modules\ReportApi\MQR\MqrAccReport;
use App\Http\Controllers\Modules\ReportApi\MQR\MQRDailyRidershipReport;
use App\Http\Controllers\Modules\ReportApi\MQR\MQRSapReport;
use App\Http\Controllers\Modules\ReportApi\MQR\MqrTravelApi;
use App\Http\Controllers\Modules\ReportApi\MQR\PreviousDayReport;
use App\Http\Controllers\Modules\ReportApi\OL\OlAccReport;
use App\Http\Controllers\Modules\ReportApi\OL\OlFicoReport;
use App\Http\Controllers\Modules\ReportApi\OL\OlSapReport;
use App\Http\Controllers\Modules\ReportApi\PQR\PqrAccReport;
use App\Http\Controllers\Modules\ReportApi\PQR\PQRDailyRidershipReport;
use App\Http\Controllers\Modules\ReportApi\PQR\PQRPreviousDayReport;
use App\Http\Controllers\Modules\ReportApi\PQR\PQRSapReport;
use App\Http\Controllers\Modules\ReportApi\PQR\PqrTravelApi;
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

Route::post('/cl/initialisation', [ClInitialisation::class, 'initialisation']);
Route::post('get/params', [PassController::class, 'getParamsForPass']);

/*DOWNLOAD AND CHECK UPDATE OF FIRMWARE*/
Route::post('/checkUpdate', [Firmware::class, 'checkUpdate']);
Route::get('/getFirmware/{uploadId}', [Firmware::class, 'getFirmware']);

/* GET CONFIGURATION V0 */
Route::post('config', [ConfigApiController::class, 'getConfig']);

/* API CONTROLLER V1 */
Route::post('v1/config', [NewConfigApiController::class, 'getConfig']);
Route::post('v2/config', [V2ConfigApiController::class, 'getConfig']);

/* SETTLE OPEN LOOP TRANSACTION */
Route::post('settleOlTransaction', [SettleOlTransaction::class, 'setOlTransaction']);

/* SETTLE SV ACCOUNTING */
Route::post('syncOlAccTrans', [OlSvAccounting::class, 'OlSvAccounting']);

/* FOR GETTING TERMINAL ID */
Route::post('tidDetails', [TidController::class, 'tidDetails']);

/*FOR GETTING CARD IN CARD BLACKLIST */
Route::get('/get/blacklisted/cardDetail/{id}', [CardBlacklistController::class, 'search']);

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
/* ISSUANCE API */
Route::post('/otp/send', [IssuanceApiManager::class, 'sendOtp']);
Route::post('/otp/verify', [IssuanceApiManager::class, 'verifyOtp']);
Route::post('/kyc/details', [IssuanceApiManager::class, 'kycDetail']);
Route::post('/kyc/submit', [IssuanceApiManager::class, 'submitKyc']);
Route::post('/card/activation', [IssuanceApiManager::class, 'activateCard']);
Route::post('/card/status', [IssuanceApiManager::class, 'activationStatus']);

/* ACQUIRER API */
Route::post('/verifyTerminal', [AcqApiManager::class, 'verifyTerminal']);
Route::post('/moneyLoad', [AcqApiManager::class, 'moneyLoad']);
Route::post('/sale', [AcqApiManager::class, 'sale']);
Route::post('/balanceUpdate', [AcqApiManager::class, 'balanceUpdate']);
Route::post('/void', [AcqApiManager::class, 'voidTrans']);
Route::post('/serviceCreation', [AcqApiManager::class, 'serviceCreation']);
Route::post('/updateReceiptAndRevertLastTxn', [AcqApiManager::class, 'updateReceiptAndRevertLastTxn']);

/* PAYTM SETTLEMENT */
Route::post('/settlement', [Settlement::class, 'settlement']);

/*CLOSE LOOP API*/
Route::get('/clSnMapping', [ClSnMapping::class, 'index']);

/* SETTLE CL SV & TP ACCOUNTING TRANSACTION */
Route::post('/sync/cl/accounting', [ClAccounting::class, 'ClAccounting']);

/* SETTLE CL SV & TP VALIDATION TRANSACTION */
Route::post('/sync/cl/validation', [ClValidation::class, 'setClTransaction']);

/* CL INDRA CARD REP */
Route::post('/cl/indra/card/rep', [ClIndraCardReplacement::class, 'store']);

/* GETTING CARD DATA FOR REPLACEMENT */
Route::get('/cl/card/rep/{engravedId}', [ClCardReplacement::class, 'getCardData']);

/* TRIP PASS*/
Route::post('/cl/tp/exit/revenue', [TripPassExitRevenue::class, 'tpExitRevenue']);
Route::post('/cl/tp/stale/revenue/atek', [TripPassExitRevenue::class, 'tpStaleRevenueAtek']);
Route::post('/cl/tp/stale/revenue/indra', [TripPassExitRevenue::class, 'tpStaleRevenueIndra']);
Route::post('/cl/tp/stale/revenue/ul', [TripPassExitRevenue::class, 'tpStaleUL']);

/* STORE VALUE PASS */
Route::post('/cl/sv/stale/revenue/indra', [StoreValueExitRevenue::class, 'storeValueStaleIndra']);
Route::post('/cl/sv/stale/revenue/atek', [StoreValueExitRevenue::class, 'storeValueStaleAtek']);
Route::post('/cl/sv/exit/revenue', [StoreValueExitRevenue::class, 'svExitRevenue']);

/* AFC AUDIT API */
Route::post('/cl/audit/sv/', [StoreValueAuditApi::class, 'lag']);
Route::get('/cl/audit/tp/{startDate}/{endDate}', [TripPassAuditApi::class, 'index']);

Route::post('/cl/train/load', [KnowYourLoad::class, 'cl']);
Route::post('/ol/train/load', [KnowYourLoad::class, 'ol']);

/* REPORT API'S*/
Route::middleware(['basic_auth'])->group(function () {

    /* OPEN LOOP API */
    Route::post('/oldailyRidership', [DailyRidership::class, 'index']);
    Route::post('/olcashCollection', [CashCollection::class, 'index']);
    Route::post('/olrevenue', [Revenue::class, 'index']);
    Route::post('/olPrevDay', [Revenue::class, 'olPrevDay']);
    Route::post('/olValReport', [TravelApiController::class, 'getReport']);
    Route::post('/olSap', [OlSapReport::class, 'index']);

    /* OL TOM API */
    Route::post('/olSaleReport', [OlAccReport::class, 'olSaleReport']);
    Route::post('/olSvAccReport', [OlAccReport::class, 'olSvAccReport']);

    /* MMOPL OL SAP FICO POSTING */
    Route::get('/olFicoReport', [OlFicoReport::class, 'index']);
    Route::get('/clFicoReport', [ClFicoReport::class, 'index']);

    /* CLOSED LOOP API */
    Route::post('/cl/Daily/Ridership', [DailyRidershipReport::class, 'dailyRidership']);
    Route::post('/cl/revenue', [RevenueReport::class, 'revenue']);
    Route::post('/cl/cardDetail/mobileNumber', [CardDetail::class, 'cardDetailUsingMobileNumber']);
    Route::post('/cl/cardDetail/cardNumber', [CardDetail::class, 'cardDetailUsingCardNumber']);
    Route::post('/clSap', [ClSapReport::class, 'index']);
    Route::post('/clSvValReport', [ClTravelApi::class, 'svValReport']);
    Route::post('/clTpValReport', [ClTravelApi::class, 'tpValReport']);

    /* TOM API */
    Route::post('/clSvAccReport', [ClAccReport::class, 'svAccReport']);
    Route::post('/clTpAccReport', [ClAccReport::class, 'tpAccReport']);

    /* MQR API */
    /* ACCOUNTING REPORTS TO BE ON HOLD BY L1 */
    Route::get('/mqrSjtAccReport', [MqrAccReport::class, 'sjtAccReport']);
    Route::get('/mqrRjtAccReport', [MqrAccReport::class, 'rjtAccReport']);
    Route::get('/mqrSvAccReport', [MqrAccReport::class, 'svAccReport']);
    Route::get('/mqrTpAccReport', [MqrAccReport::class, 'tptAccReport']);

    /* MQR BOARDING ALIGHTING (TRAVEL API)*/
    Route::get('mqr/sjtValReport', [MqrTravelApi::class, 'sjtValReport']);
    Route::get('mqr/rjtValReport', [MqrTravelApi::class, 'rjtValReport']);
    Route::get('mqr/svValReport', [MqrTravelApi::class, 'svValReport']);
    Route::get('mqr/tpValReport', [MqrTravelApi::class, 'tpValReport']);

    /* MQR BOARDING ALIGHTING (FOR DUMP DATA ONLY) */
    Route::get('mqr/svVal2Report', [MqrTravelApi::class, 'svVal2Report']);
    Route::get('mqr/tpVal2Report', [MqrTravelApi::class, 'tpVal2Report']);

    /* MQR HOURLY REPORT (DAILY RIDERSHIP) */
    Route::post('/mqr/Daily/Ridership', [MQRDailyRidershipReport::class, 'dailyRidership']); /*Hourly Report*/
    Route::post('/mqr/PrevDay', [PreviousDayReport::class, 'MqrPrevDay']);  /*Previous Day*/
    Route::post('/mqrSap', [MQRSapReport::class, 'index']);

    /* PQR API */
    /* ACCOUNTING REPORTS */
    Route::get('/pqrSjtAccReport', [PqrAccReport::class, 'sjtAccReport']);
    Route::get('/pqrRjtAccReport', [PqrAccReport::class, 'rjtAccReport']);

    /* PQR BOARDING ALIGHTING (TRAVEL API)*/
    Route::get('pqr/sjtValReport', [PqrTravelApi::class, 'sjtValReport']);
    Route::get('pqr/rjtValReport', [PqrTravelApi::class, 'rjtValReport']);

    /* PQR HOURLY REPORT (DAILY RIDERSHIP) */
    Route::post('/pqr/Daily/Ridership', [PQRDailyRidershipReport::class, 'dailyRidership']); /*Hourly Report*/
    Route::post('/pqr/PrevDay', [PQRPreviousDayReport::class, 'PqrPrevDay']);  /*Previous Day*/
    Route::post('/pqrSap', [PQRSapReport::class, 'index']);

});
