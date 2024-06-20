<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Modules\Acq\AcqParamController;
use App\Http\Controllers\Modules\Authentication\LoginController;
use App\Http\Controllers\Modules\CardBlacklist\CardBlacklistController;
use App\Http\Controllers\Modules\CardSnMapping\CardSnMappingController;
use App\Http\Controllers\Modules\CardType\CardTypeController;
use App\Http\Controllers\Modules\Configuration\ConfigController;
use App\Http\Controllers\Modules\Equipment\EquipmentController;
use App\Http\Controllers\Modules\Fare\FareController;
use App\Http\Controllers\Modules\Firmware\FirmwareController;
use App\Http\Controllers\Modules\Firmware\FirmwarePublish;
use App\Http\Controllers\Modules\Operator\OperatorController;
use App\Http\Controllers\Modules\Pass\PassController;
use App\Http\Controllers\Modules\Product\ProductController;
use App\Http\Controllers\Modules\Station\StationController;
use App\Http\Controllers\Modules\Tid\TidController;
use App\Http\Controllers\Modules\User\UserController;
use App\Http\Controllers\Modules\User\UserPrivilege;
use App\Http\Controllers\Modules\User\UserRole;
use App\Http\Controllers\Modules\Vendor\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index']);
Route::post('/', [LoginController::class, 'verify']);

Route::get('/dashboard', [DashboardController::class, 'index']);

Route::get('/stations', [StationController::class, 'index']);
Route::get('/stations/create', [StationController::class, 'create']);
Route::post('/stations', [StationController::class, 'store']);
Route::get('/stations/edit/{id}', [StationController::class, 'edit']);
Route::post('/stations/edit/{id}', [StationController::class, 'update']);

// EQUIPMENT INVENTORY
Route::get('/equipments', [EquipmentController::class, 'index']);
Route::get('/equipments/create', [EquipmentController::class, 'create']);
Route::post('/equipments', [EquipmentController::class, 'store']);
Route::get('/equipments/edit/{id}', [EquipmentController::class, 'edit']);
Route::post('/equipments/edit/{id}', [EquipmentController::class, 'update']);

// FARES
Route::get('/fares', [FareController::class, 'index']);
Route::get('/fares/create', [FareController::class, 'create']);
Route::post('/fares', [FareController::class, 'store']);
Route::get('/fares/edit/{id}', [FareController::class, 'edit']);
Route::post('/fares/edit/{id}', [FareController::class, 'update']);

// PRODUCTS
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/create', [ProductController::class, 'create']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/edit/{id}', [ProductController::class, 'edit']);
Route::post('/products/edit/{id}', [ProductController::class, 'update']);

// PASSES
Route::get('/pass', [PassController::class, 'index']);
Route::get('/pass/create', [PassController::class, 'create']);
Route::post('/pass', [PassController::class, 'store']);
Route::get('/pass/edit/{id}', [PassController::class, 'edit']);
Route::post('/pass/edit/{id}', [PassController::class, 'update']);
Route::get('/pass/parameters', [PassController::class, 'params']);
Route::post('/pass/parameters', [PassController::class, 'updateParams']);

// VENDORS
Route::get('/vendors', [VendorController::class, 'index']);
Route::get('/vendors/create', [VendorController::class, 'create']);
Route::post('/vendors', [VendorController::class, 'store']);
Route::get('/vendors/edit/{id}', [VendorController::class, 'edit']);
Route::post('/vendors/edit/{id}', [VendorController::class, 'update']);

// USERS
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/create', [UserController::class, 'create']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/edit/{id}', [UserController::class, 'edit']);
Route::post('/users/edit/{id}', [UserController::class, 'update']);

// USER ROLES
Route::get('/userRole', [UserRole::class, 'index']);
Route::get('/userRole/create', [UserRole::class, 'create']);
Route::post('/userRole', [UserRole::class, 'store']);
Route::get('/userRole/edit/{id}', [UserRole::class, 'edit']);
Route::post('/userRole/edit/{id}', [UserRole::class, 'update']);

/*USER PRIVILEGE*/
Route::get('/userPrivilege', [UserPrivilege::class, 'index']);
Route::get('/userPrivilege/create', [UserPrivilege::class, 'create']);
Route::post('/userPrivilege', [UserPrivilege::class, 'store']);
Route::get('/userPrivilege/edit/{id}', [UserPrivilege::class, 'edit']);
Route::post('/userPrivilege/edit/{id}', [UserPrivilege::class, 'update']);

/*CONFIGURATION*/
Route::get('/config', [ConfigController::class, 'index']);
Route::get('config/create/{id}', [ConfigController::class, 'create']);
Route::get('/publish', [ConfigController::class, 'PublishIndex']);
Route::post('/publish/create', [ConfigController::class, 'publishCreate']);

/*TID INVENTORY*/
Route::get('/tids', [TidController::class, 'index']);
Route::get('/tid/create', [TidController::class, 'create']);
Route::post('/tids', [TidController::class, 'store']);
Route::get('/tid/edit/{id}', [TidController::class, 'edit']);
Route::post('/tid/edit/{id}', [TidController::class, 'update']);

/*ACQ PARAMETERS*/
Route::get('/acq', [AcqParamController::class, 'index']);
Route::get('/acq/create', [AcqParamController::class, 'create']);
Route::post('/acq', [AcqParamController::class, 'store']);
Route::get('/acq/edit/{id}', [AcqParamController::class, 'edit']);
Route::post('/acq/edit/{id}', [AcqParamController::class, 'update']);

/* CARD TYPE */
Route::get('/cardType', [CardTypeController::class, 'index']);
Route::get('/cardType/create', [CardTypeController::class, 'create']);
Route::post('/cardType', [CardTypeController::class, 'store']);
Route::get('/cardType/edit/{id}', [CardTypeController::class, 'edit']);
Route::post('/cardType/edit/{id}', [CardTypeController::class, 'update']);

/* FIRMWARE */
Route::get('/firmware', [FirmwareController::class, 'index']);
Route::get('/firmwarePublish', [FirmwarePublish::class, 'index']);
Route::post('/firmwarePublish', [FirmwarePublish::class, 'store']);
Route::post('/saveFile', [FirmwareController::class, 'uploadFile']);

//CARD BLACKLIST
Route::get('/card/blacklist', [CardBlacklistController::class, 'index']);
Route::post('/card/blacklist', [CardBlacklistController::class, 'store']);
Route::post('/card/blacklist/delete/{id}', [CardBlacklistController::class, 'delete']);

// CARD SN MAPPING
Route::get('/card/sn/mapping', [CardSnMappingController::class, 'index']);
Route::post('/card/sn/mapping', [CardSnMappingController::class, 'store']);

/*OPERATOR*/
Route::get('/operators', [OperatorController::class, 'index']);
