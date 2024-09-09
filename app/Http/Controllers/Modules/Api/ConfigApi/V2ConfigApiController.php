<?php

namespace App\Http\Controllers\Modules\Api\ConfigApi;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class V2ConfigApiController extends Controller
{
    /* MASTER FUNCTION */
    public function getConfig(Request $request)
    {
        $eq_type_id = $request->input('eq_type_id');
        $eq_id = $request->input('eq_id');

        if ($eq_type_id == 1) return $this->getAGConfig($request);
        else if ($eq_type_id == 2) return $this->getTOMConfig($request);
        else if ($eq_type_id == 4) return $this->getSCSConfig($request);
        else if ($eq_id != null || $eq_id != "" || $eq_type_id == 6) return $this->getEDCConfig($request);
        else if ($eq_type_id == 7) return $this->getReaderConfig($request);

        return response([
            'status' => false,
            'code' => 101,
            'error' => "Equipment identification failed !"
        ]);

    }

    /* FOR SCS CONFIGURATION */
    private function getSCSConfig(Request $request)
    {
        $ip = $request->input('ip');
        $config_version = $request->input('config_version');

        $equipment = DB::table('equipment_inventory as ei')
            ->join('station_inventory as stn', 'stn.stn_id', '=', 'ei.stn_id')
            ->where('ip_address', '=', $ip)
            ->where('eq_type_id', '=', 4)
            ->select([
                'ei.eq_type_id',
                'ei.eq_num',
                'ei.status',
                'ei.eq_id',
                'ei.stn_id',
                'stn.stn_name',
                'ei.eq_version',
            ])
            ->first();

        if ($equipment == null) {
            return response([
                'status' => false,
                'code' => 102,
                'error' => 'No config is available!'
            ]);
        }

        $configs = DB::table('config_publish')
            ->where('equipment_id', '=', $equipment->eq_id)
            ->where('is_sent', '=', false)
            ->get();

        $configResponse = [];

        if ($configs->count() > 0) {

            foreach ($configs as $config) {

                if ($config->config_id == 1) {

                    if ($config->config_version != $config_version) {

                        $configResponse['config'] = $equipment;

                        $configResponse['equipments'] = DB::table('equipment_inventory as ei')
                            ->join('station_inventory as stn', 'stn.stn_id', '=', 'ei.stn_id')
                            ->where('ei.stn_id', '=', $equipment->stn_id)
                            ->where('ei.eq_type_id', '!=', 4)
                            ->select([
                                'ei.eq_type_id',
                                'ei.eq_mode_id',
                                'ei.eq_role_id',
                                'ei.eq_num',
                                'ei.eq_id',
                                'ei.eq_location_id',
                                'ei.cord_x',
                                'ei.cord_y',
                                'ei.status',
                                'ei.stn_id',
                                'stn_name',
                                'ei.eq_version',
                                'ei.ip_address'
                            ])
                            ->get();


                    } else {
                        $configResponse['version']['eq_version'] = $config_version;
                    }
                }

                if ($config->config_id == 5) {
                    $configResponse['users'] = DB::table('user_inventory')
                        ->where('status', '=', true)
                        ->orderBy('user_id', 'ASC')
                        ->select([
                            'user_id',
                            'first_name',
                            'middle_name',
                            'last_name',
                            'emp_mobile',
                            'emp_email',
                            'emp_dob',
                            'user_login',
                            'user_pwd',
                            'status',
                        ])
                        ->get();
                }

            }

            DB::table('config_publish')
                ->where('equipment_id', '=', $equipment->eq_id)
                ->update([
                    'is_sent' => true,
                    'updated_at' => Carbon::now()
                ]);

            return response([
                'status' => true,
                'code' => 100,
                'data' => $configResponse
            ]);

        }

        return response([
            'status' => false,
            'code' => 102,
            'error' => 'No config is available!'
        ]);
    }

    /* FOR AG CONFIGURATION*/
    private function getAGConfig(Request $request)
    {
        $ip = $request->input('ip');
        $config_version = $request->input('config_version');
        $fare_version = $request->input('fare_version');
        $pass_version = $request->input('pass_version');
        $cl_black_list_version = $request->input('cl_blacklist_version');


        $equipment = DB::table('equipment_inventory as ei')
            ->join('station_inventory as stn', 'stn.stn_id', '=', 'ei.stn_id')
            ->where('eq_type_id', '=', 1)
            ->where('ip_address', '=', $ip)
            ->select(['ei.*', 'stn.stn_name'])
            ->first();

        if ($equipment == null) {
            return response([
                'status' => false,
                'code' => 102,
                'error' => 'No config is available!'
            ]);
        }

        $configs = DB::table('config_publish')
            ->where('equipment_id', '=', $equipment->eq_id)
            ->where('is_sent', '=', false)
            ->get();

        $configResponse = [];

        if ($configs->count() > 0) {

            $configResponse['activation_time'] = $configs[0]->activation_time;

            foreach ($configs as $config) {

                if ($config->config_id == 1) {

                    if ($config->config_version != $config_version) {

                        $scs = DB::table('equipment_inventory')
                            ->where('stn_id', '=', $equipment->stn_id)
                            ->where('eq_type_id', '=', 1)
                            ->first();

                        $eq_data = json_decode($equipment->eq_data);
                        $eq_data->stn_name = $equipment->stn_name;
                        $eq_data->scs_ip_add = $scs->ip_address;

                        $configResponse['config'] = $eq_data;
                        $configResponse['version']['eq_version'] = $config->config_version;

                    } else {
                        $configResponse['version']['eq_version'] = $config_version;
                    }

                }

                if ($config->config_id == 2) {

                    if ($config->config_version != $fare_version) {

                        $FareData = DB::table('config_gen')
                            ->where('config_id', '=', 2)
                            ->where('config_version', '=', $config->config_version)
                            ->value('config_data');

                        $data = json_decode($FareData, true);
                        $NewData = collect($data)
                            ->sortBy('fare_table_id')
                            ->values()
                            ->all();

                        $configResponse['fares'] = $NewData;
                        $configResponse['version']['fare_version'] = $config->config_version;

                    } else {
                        $configResponse['version']['fare_version'] = $fare_version;
                    }

                }

                if ($config->config_id == 4) {
                    if ($config->config_version != $pass_version) {

                        $PassData = DB::table('config_gen')
                            ->where('config_id', '=', 4)
                            ->where('config_version', '=', $config->config_version)
                            ->value('config_data');

                        $data = json_decode($PassData, true);
                        $NewData = collect($data)
                            ->sortBy('pass_inv_id')
                            ->values()
                            ->all();

                        $configResponse['passes'] = $NewData;
                        $configResponse['version']['pass_version'] = $config->config_version;
                    } else {
                        $configResponse['version']['pass_version'] = $pass_version;
                    }
                }

                /* FOR CARD BLACKLIST CONFIGURATION */
                if ($config->config_id == 7) {

                    if ($config->config_version != $cl_black_list_version) {

                        $Cl_blacklist = DB::table('cl_blacklist')
                            ->select('chip_id')
                            ->distinct('chip_id')
                            ->get()
                            ->toJson();

                        $data = json_decode($Cl_blacklist, true);
                        $NewData = collect($data)
                            ->sortBy('chip_id')
                            ->values()
                            ->all();

                        $configResponse['cl_blacklist'] = $NewData;

                    }
                    $configResponse['version']['cl_blacklist_version'] = $config->config_version;
                }

            }

            DB::table('config_publish')
                ->where('equipment_id', '=', $equipment->eq_id)
                ->update([
                    'is_sent' => true,
                    'updated_at' => Carbon::now()
                ]);

            return response([
                'status' => true,
                'code' => 100,
                'data' => $configResponse
            ]);

        }

        return response([
            'status' => false,
            'code' => 102,
            'error' => 'No config is available!'
        ]);

    }

    /* FOR TOM CONFIGURATION */
    private function getTOMConfig(Request $request)
    {
        $ip           = $request->input('ip');
        $fare_version = $request->input('fare_version');
        $pass_version = $request->input('pass_version');

        $config_response = [];

        /* FETCHING EQUIPMENT DETAIL */
        $equipment = DB::table('equipment_inventory as ei')
            ->join('station_inventory as stn', 'stn.stn_id', '=', 'ei.stn_id')
            ->where('ip_address', '=', $ip)
            ->where('eq_type_id', '=', 2)
            ->select([
                'eq_type_id',
                'eq_mode_id',
                'eq_role_id',
                'eq_num',
                'ei.stn_id',
                'eq_id',
                'ei.status',
                'ei.eq_version',
                'stn_name'
            ])
            ->first();

        if ($equipment == null || $equipment == "") {
            return response([
                'status' => false,
                'code'   => 102,
                'error'  => 'Equipment not found!'
            ]);
        }

        $published_config = DB::table('config_publish')
            ->where('equipment_id', '=', $equipment->eq_id)
            ->where('is_sent', '=', false)
            ->get();

        if ($published_config->count() == 0) {
            return response([
                'status' => false,
                'code' => 102,
                'error' => 'No config is available!'
            ]);
        }


        if (!empty($published_config) && isset($published_config[0])) {
            if ($published_config[0]->activation_time == null) {
                $config_response['activation_time'] = Carbon::now()->timestamp * 1000;
            } else {
                $datetime   = $published_config[0]->activation_time;
                $epochTime  = strtotime($datetime);
                $config_response['activation_time'] = $epochTime * 1000;
            }
        } else {
            $config_response['activation_time'] = null;
        }



        $scsIp = DB::table('equipment_inventory as ei')
            ->where('stn_id', '=', $equipment->stn_id)
            ->where('eq_type_id', '=', 4)
            ->select('ip_address as scs_ip_add')
            ->first();

        /* IF NO SCS IP IS AVAILABLE */
        if ($scsIp == null) {
            return response([
                'status' => false,
                'code'   => 102,
                'error'  => 'SCS configuration not found for equipment!'
            ]);
        }

        /* CREATING CONFIG ARRAY FOR RESPECTIVE EQUIPMENT DATA CAN SAVE */
        $config_response['config']                   = (array)$equipment;
        $config_response['config']['scs_ip_add']     = $scsIp->scs_ip_add;
        $config_response['config']['config_version'] = $equipment->eq_version;

        /* FOR FARE CONFIGURATION */
        $fare_config = $published_config->filter(function ($item) {
            return $item->config_id == 2;
        })->first();
        if ($fare_config != null && $fare_version != $fare_config->config_version) {
            $fares = DB::table('config_gen')
                ->where('config_id', '=', 2)
                ->where('config_version', '=', $fare_config->config_version)
                ->value('config_data');
            $config_response['fares'] = json_decode($fares, true);
            $config_response['config']['fare_version'] = $fare_config->config_version;
        } else {
            $config_response['config']['fare_version'] = $fare_version;
        }

        /* FOR STATION CONFIGURATION */
        $station_config = $published_config->filter(function ($item) {
            return $item->config_id == 3;
        })->first();
        if ($station_config != null) {
            $config_response['stations'] = DB::table('station_inventory')
                ->orderBy('stn_inv_id', 'ASC')
                ->where('status', '=', true)
                ->select([
                    'stn_id',
                    'stn_name',
                    'stn_short_name',
                    'stn_national_lang',
                    'stn_regional_lang',
                ])
                ->orderBy('stn_id', 'ASC')
                ->get();
        }

       /* FOR PASS CONFIGURATION */
        $pass_config = $published_config->filter(function ($item) {
            return $item->config_id == 4;
        })->first();
        if ($pass_config != null && $pass_version != $pass_config->config_version) {
            $passes = DB::table('config_gen')
                ->where('config_id', '=', 4)
                ->where('config_version', '=', $pass_config->config_version)
                ->value('config_data');
            $config_response['passes'] = json_decode($passes, true);
            $config_response['config']['pass_version'] = $pass_config->config_version;
        } else {
            $config_response['config']['pass_version'] = $pass_version;
        }

        /* FOR USER CONFIGURATION */
        $user_config = $published_config->filter(function ($item) {
            return $item->config_id == 5;
        })->first();
        if ($user_config != null) {
            $config_response['users'] = DB::table('user_inventory')
                ->where('status', '=', true)
                ->orderBy('user_id', 'ASC')
                ->select([
                    'user_inv_id',
                    'user_id',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'designation',
                    'emp_id',
                    'emp_mobile',
                    'emp_email',
                    'emp_gender',
                    'emp_dob',
                    'user_login',
                    'user_pwd',
                    'status',
                ])
                ->get();
        }

        /* FOR CARD TYPES */
        $config_response['cards'] = DB::table('card_type')
            ->where('status', '=', true)
            ->select([
                'card_type_id',
                'media_type_id',
                'card_name',
                'description',
                'card_pro_id',
                'card_fee',
                'card_sec',
                'status',
                'card_sec_refund_permit',
                'card_sec_refund_charges',
                'ps_type_id'
            ])
            ->orderBy('card_id', 'ASC')
            ->get();

        /* UPDATING THE TABLE AFTER CONFIGURATION SENT SUCCESSFULLY */
        DB::table('config_publish')
            ->where('equipment_id', '=', $equipment->eq_id)
            ->update([
                'is_sent'    => true,
                'updated_at' => Carbon::now()
            ]);

        return response([
            'status' => true,
            'code' => 200,
            'data' => $config_response
        ]);

    }

    /* FOR EDC CONFIGURATION */
    private function getEDCConfig(Request $request)
    {
        $serial_no              = $request->input('emv_serial_no');
        $eq_type_id             = $request->input('eq_type_id');
        $eq_id                  = $request->input('eq_id');
        $cl_black_list_version  = $request->input('cl_blacklist_version');
        $pass_version           = $request->input('pass_version');
        $configResponse         = [];

        /* FETCHING EQUIPMENT DETAIL FOR EDC CONFIG */
        $equipment = DB::table('equipment_inventory as ei')
            ->join('station_inventory as stn', 'stn.stn_id', '=', 'ei.stn_id')
            ->where('eq_id', '=', $eq_id)
            ->where('eq_type_id', '=', 2)
            ->select([
                'eq_id',
                'ei.stn_id',
                'stn_name',
                'ei.eq_version'
            ])
            ->first();

        $readerData = DB::table('tid_inv')
            ->join('acq_param', 'acq_param.eq_type_id', '=', 'tid_inv.eq_type_id')
            ->where('tid_inv.emv_serial_no', '=', $serial_no)
            ->where('tid_inv.eq_type_id', '=', $eq_type_id)
            ->select([
                'tid_inv.emv_tid as terminal_id',
                'acq_param.acq_mid as merchant_id',
                'acq_param.client_id as client_id',
                'acq_param.acq_id as acquirer_id',
                'acq_param.acq_name as acquirer_name',
                'acq_param.operator_id',
            ])
            ->first();

        $readerDataArray = $readerData ? (array)$readerData : [];
        $equipmentArray = $equipment ? (array)$equipment : [];

        /**
         * Fetch and combine data from multiple sources into a unified JSON response.
         *
         * Retrieves data from:
         * - `tid_inv` table based on serial number and equipment type ID.
         * - `equipment_inventory` and `station_inventory` tables based on IP address and equipment type.
         * - `acquirer_param` table based on acquirer ID.
         *
         * The combined data is returned as a single JSON object.
         *
         * @param Request $request The HTTP request containing input parameters.
         * @return JsonResponse The combined data in JSON format.
         */

        $edcData = array_merge($equipmentArray, $readerDataArray);

        if ($readerData == null || $readerData == "") {
            return response([
                'status'    => false,
                'code'      => 103,
                'error'     => 'Invalid serial number !'
            ]);
        }

        if ($eq_id == null) {
            return response([
                'status' => false,
                'code'   => 103,
                'error'  => 'EDC Does Not Exist !'
            ]);
        }

        if ($equipment == null || $equipment == "") {

            return response([
                'status' => false,
                'code'   => 102,
                'error'  => 'No Equipment is Found!'
            ]);

        }

        $published_config = DB::table('config_publish')
            ->where('equipment_id', '=', $equipment->eq_id)
            ->where('is_edc_sync', '=', false)
            ->get();

        if ($published_config->count() == 0) {
            return response([
                'status'  => false,
                'code'    => 100,
                'message' => "No config is Available !"
            ]);

        }

        if (!empty($published_config) && isset($published_config[0])) {
            if ($published_config[0]->activation_time == null) {
                $configResponse['activation_time'] = Carbon::now()->timestamp * 1000;
            } else {
                $datetime   = $published_config[0]->activation_time;
                $epochTime  = strtotime($datetime);
                $configResponse['activation_time'] = $epochTime * 1000;
            }
        } else {
            $configResponse['activation_time'] = Carbon::now()->timestamp * 1000;
        }

        /* CREATING CONFIG ARRAY FOR RESPECTIVE EQUIPMENT DATA CAN SAVE */
        $configResponse['config']                   = $edcData;
        $configResponse['config']['config_version'] = $equipment->eq_version;

        /* FOR PASS CONFIGURATION */
        $pass_config = $published_config->filter(function ($item) {
            return $item->config_id == 4;
        })->first();
        if ($pass_config != null && $pass_version != $pass_config->config_version) {
            $passes = DB::table('config_gen')
                ->where('config_id', '=', 4)
                ->where('config_version', '=', $pass_config->config_version)
                ->value('config_data');
            $configResponse['passes'] = json_decode($passes, true);
            $configResponse['config']['pass_version'] = $pass_config->config_version;
        } else {
            $configResponse['config']['pass_version'] = $pass_version;
        }

        /* FOR CARD TYPES */
        $configResponse['cards'] = DB::table('card_type')
            ->where('status', '=', true)
            ->select([
                'card_type_id',
                'media_type_id',
                'card_name',
                'description',
                'card_pro_id',
                'card_fee',
                'card_sec',
                'status',
                'card_sec_refund_permit',
                'card_sec_refund_charges',
                'ps_type_id'
            ])
            ->orderBy('card_id', 'ASC')
            ->get();

        /* FOR CARD BLACKLIST CONFIGURATION */
        $card_blacklist_config = $published_config->filter(function ($item) {
            return $item->config_id == 7;
        })->first();
        if ($card_blacklist_config != null && $cl_black_list_version != $card_blacklist_config->config_version) {

            $Cl_blacklist = DB::table('cl_blacklist')
                ->select('chip_id')
                ->distinct('chip_id')
                ->get()
                ->toJson();

            $configResponse['cl_blacklist'] = json_decode($Cl_blacklist, true);
            $configResponse['config']['cl_blacklist_version'] = $card_blacklist_config->config_version;
        } else {
            $configResponse['config']['cl_blacklist_version'] = $cl_black_list_version;
        }

        DB::table('config_publish')
            ->where('equipment_id', '=', $equipment->eq_id)
            ->update([
                'is_edc_sync' => true,
                'updated_at' => Carbon::now()
            ]);

        return response([
            'status' => true,
            'code' => 200,
            'data' => $configResponse
        ]);

    }

    /* FOR READER CONFIGURATION */
    private function getReaderConfig(Request $request)
    {
        $serial_no = $request->input('serial_no');
        $eq_id = $request->input('eq_id');

        $readerData = DB::table('tid_inv')
            ->where('emv_serial_no', '=', $serial_no)
            ->first();

        if ($readerData == null) {
            return response([
                'status' => false,
                'code' => 103,
                'error' => 'Invalid serial number !'
            ]);
        }

        if ($eq_id != null) {

            if ($eq_id != $readerData->eq_id) {

                DB::table('tid_hist')->insert([
                    'eq_id' => $readerData->eq_id,
                    'emv_serial_no' => $readerData->emv_serial_no,
                    'start_date' => $readerData->start_date,
                    'end_date' => now()
                ]);

                DB::table('tid_inv')->update(['eq_id' => $eq_id]);

            }
        }

        return response([
            'status' => true,
            'code' => 100,
            'config' => DB::table('tid_inv')
                ->join('acq_param', 'acq_param.acq_id', '=', 'tid_inv.acq_id')
                ->where('emv_serial_no', '=', $serial_no)
                ->first()
        ]);

    }

}
