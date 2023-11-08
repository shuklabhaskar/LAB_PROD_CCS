<?php

namespace App\Http\Controllers\Modules\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigApiController extends Controller
{
    public function getConfig(Request $request)
    {
        $eq_type_id = $request->input('eq_type_id');

        if ($eq_type_id == 1) return $this->getAGConfig($request);
        else if ($eq_type_id == 2) return $this->getTOMConfig($request);
        else if ($eq_type_id == 4) return $this->getSCSConfig($request);
        else if ($eq_type_id == 6) return $this->getEDCConfig($request);
        else if ($eq_type_id == 7) return $this->getReaderConfig($request);
        else if ($eq_type_id == 8) return $this->getAcquirerConfig($request);

        return response([
            'status'    => false,
            'code'      => 101,
            'error'     => "Equipment identification failed !"
        ]);

    }

    private function getSCSConfig(Request $request)
    {
        $ip = $request->input('ip');
        $config_version = $request->input('config_version');

        $equipment = DB::table('equipment_inventory as ei')
            ->join('station_inventory as stn', 'stn.stn_id', '=', 'ei.stn_id')
            ->where('ip_address', '=', $ip)
            ->where('eq_type_id', '=', 4)
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

            foreach ($configs as $config) {

                if ($config->config_id == 1) {

                    if ($config->config_version != $config_version) {
                        $scs = DB::table('equipment_inventory')->where('stn_id', '=', $equipment->stn_id)
                            ->where('eq_type_id', '=', 4)->first();
                        $eq_data = json_decode($equipment->eq_data);
                        $eq_data->stn_name = $equipment->stn_name;
                        $eq_data->scs_ip_add = $scs->ip_address;
                        $configResponse['config'] = $eq_data;

                        $configResponse['equipments'] = DB::table('equipment_inventory')
                            ->where('stn_id', '=', $equipment->stn_id)
                            ->where('eq_type_id','=',1)
                            ->select([
                                'eq_type_id',
                                'eq_mode_id',
                                'eq_role_id',
                                'eq_id',
                                'eq_location_id',
                                'cord_x',
                                'cord_y',
                                'status',
                                'is_blacklisted',
                            ])
                            ->get();

                        $configResponse['users'] = DB::table('user_inventory')
                           ->select([
                                'user_login',
                                'user_pwd',
                           ])->get();

                        $configResponse['version']['eq_version'] = $config->config_version;

                    } else {
                        $configResponse['version']['eq_version'] = $config_version;
                    }
                }

            }

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
    private function getAGConfig(Request $request)
    {
        $ip = $request->input('ip');
        $config_version = $request->input('config_version');
        $fare_version = $request->input('fare_version');
        $pass_version = $request->input('pass_version');

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

            foreach ($configs as $config) {

                if ($config->config_id == 1) {
                    if ($config->config_version != $config_version) {
                        $scs = DB::table('equipment_inventory')->where('stn_id', '=', $equipment->stn_id)
                            ->where('eq_type_id', '=', 1)->first();
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
                        $configResponse['fares'] = DB::table('fare_table')
                            ->orderBy('fare_table_id')
                            ->get();

                        $configResponse['version']['fare_version'] = $config->config_version;
                    } else {
                        $configResponse['version']['fare_version'] = $fare_version;
                    }
                }

                if ($config->config_id == 4) {
                    if ($config->config_version != $pass_version) {
                        $configResponse['passes'] = DB::table('pass_inventory')
                            ->orderBy('pass_inv_id','ASC')
                            ->get();
                        $configResponse['version']['pass_version'] = $config->config_version;
                    }else {
                        $configResponse['version']['pass_version'] = $pass_version;
                    }
                }

            }

            DB::table('config_publish')
                ->where('equipment_id','=',$equipment->eq_id)
                ->update([
                    'is_sent'=>true,
                    'updated_at'=>Carbon::now()
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

    private function getTOMConfig(Request $request)
    {

        $ip = $request->input('ip');
        $config_version = $request->input('config_version');
        $fare_version = $request->input('fare_version');
        $pass_version = $request->input('pass_version');

        $equipment = DB::table('equipment_inventory as ei')
            ->join('station_inventory as stn', 'stn.stn_id', '=', 'ei.stn_id')
            ->where('ip_address', '=', $ip)
            ->where('eq_type_id', '=', 2)
            ->select(['eq_inv_id', 'atek_eq_id','eq_type_id','eq_mode_id','eq_role_id','eq_num','ei.stn_id','eq_id','eq_location_id','ei.description','ei.cord_x','ei.cord_y','ei.status','ei.start_date','ei.end_date','ip_address','primary_ssid','primary_ssid_pwd','backup_ssid','backup_ssid_pwd','gateway','subnetmask','is_generated','is_blacklisted','ei.created_date','ei.updated_date','ei.updated_by','ei.eq_version','stn_name'])
            ->first();


        if ($equipment==null || $equipment==""){

            return response([
                'status' => false,
                'code' => 102,
                'error' => 'No config is available!'
            ]);

        }

        $scsIp = DB::table('equipment_inventory as ei')
            ->where('stn_id', '=', $equipment->stn_id)
            ->where('eq_type_id','=',4)
            ->select('ip_address as scs_ip_add')
            ->first();

        $configs = DB::table('config_publish')
            ->where('equipment_id', '=', $equipment->eq_id)
            ->where('is_sent', '=', false)
            ->get();

        $configResponse = [];

        if ($configs->count() > 0) {

            foreach ($configs as $config) {
                /* FOR EQUIPMENT CONFIGURATION */
                if ($config->config_id == 1) {
                    if ($config->config_version != $config_version) {
                        $configResponse['config'] = $equipment;
                        $configResponse['config']->scs_ip_add    = $scsIp->scs_ip_add;
                        $configResponse['version']['eq_version'] = $config->config_version;
                    }else{
                        $configResponse['version']['eq_version'] = $config_version;
                    }
                }

                /* FOR FARES */
                if ($config->config_id == 2) {
                    if ($config->config_version != $fare_version) {
                        $configResponse['fares'] = DB::table('fare_table')
                            ->orderBy('fare_table_id','ASC')
                            ->select(['fare_table_id','source_id','destination_id','fare'])
                            ->get();
                        $configResponse['version']['fare_version'] = $config->config_version;
                    }else{
                        $configResponse['version']['fare_version'] = $fare_version;
                    }
                }

                /* FOR STATION INVENTORY */
                if ($config->config_id == 3) {
                    if ($config->config_version != $pass_version) {
                        $configResponse['stations'] = DB::table('station_inventory')->select([
                            'stn_inv_id',
                            'stn_id',
                            'stn_name',
                            'description',
                            'company_id',
                            'status',
                            'line_id',
                            'stn_short_name',
                            'stn_national_lang',
                            'stn_regional_lang',
                            'cord_x',
                            'cord_y',
                            'start_date',
                            'end_date',
                            'created_date',
                            'updated_date',
                            'updated_by',
                        ])
                            ->orderBy('stn_id','ASC')
                            ->get();
                    }
                }

                /* FOR PASS INVENTORY */
                if ($config->config_id == 4) {
                    if ($config->config_version != $pass_version) {

                       /* $configResponse['passes'] = DB::table('pass_inventory')
                            ->orderBy('pass_inv_id','ASC')
                            ->where('media_type_id','=',1)
                            ->get();*/

                        $configResponse['passes'] = DB::table('pass_inventory')
                                                    ->orderBy('pass_inv_id','ASC')
                                                    ->where(function ($query) {
                                                        $query  ->where('media_type_id', '=', 1)
                                                                ->orWhere('media_type_id', '=', 4); })
                                                    ->get();

                        $configResponse['version']['pass_version'] = $config->config_version;
                    }else{
                        $configResponse['version']['pass_version'] = $config->config_version;
                    }
                }

                /* FOR USER INVENTORY */
                if ($config->config_id == 5) {
                    $configResponse['users'] = DB::table('user_inventory')
                        ->where('status','=',true)
                        ->orderBy('user_id','ASC')
                        ->get();
                }

                /* FOR CARD TYPES */
                $configResponse['ol_products'] = DB::table('card_type')->select([
                    'card_type_id as ol_pro_id',
                    'card_pro_id as pro_id',
                    'card_name as pro_name',
                    'description as description',
                    'card_fee as pro_fee',
                    'card_sec',
                    'status as is_active',
                    'start_date',
                    'end_date as end_date',
                    'created_date as created_date',
                    'updated_by as updated_by',

                ])
                    ->orderBy('card_id','ASC')
                    ->get();

                /* FOR ACQUIRER PARAMETERS */
                $configResponse['acq_parameters'] = DB::table('acq_param')
                    ->select(['acq_param_id', 'operator_id', 'acq_id','acq_name','acq_mid','client_id','line_id','description'])
                    ->orderBy('acq_param_id','ASC')
                    ->get();

            }

            DB::table('config_publish')
                ->where('equipment_id','=',$equipment->eq_id)
                ->update([
                    'is_sent'=>true,
                    'updated_at'=>Carbon::now()
                ]);

            return response([
                'status' => true,
                'code' => 100,
                'data' => $configResponse
            ]);

        }

        return response([
            'status' => false,
            'code'   => 102,
            'error'  => 'No config is available!'
        ]);
    }

    private function getEDCConfig(Request $request)
    {
        $serial_no = $request->input('emv_serial_no');
        $eq_type_id = $request->input('eq_type_id');

        $readerData = DB::table('tid_inv')
            ->where('emv_serial_no', '=', $serial_no)
            ->where('eq_type_id','=',$eq_type_id)
            ->first();


        if ($readerData == null) {
            return response([
                'status' => false,
                'code' => 103,
                'error' => 'Invalid serial number !'
            ]);
        }

        return response([
            'status' => true,
            'code' => 100,
            'config' => DB::table('tid_inv')
                ->join('acq_param', 'acq_param.eq_type_id', '=', 'tid_inv.eq_type_id')
                ->where('tid_inv.emv_serial_no', '=', $serial_no)
                ->where('tid_inv.eq_type_id','=',$eq_type_id)
                ->select([
                    'tid_inv.emv_tid',
                    'acq_param.acq_mid',
                    'acq_param.client_id',
                    'acq_param.acq_id',
                    'acq_param.acq_name',
                    'acq_param.operator_id',
                ])
                ->first()
        ]);

    }

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

    private function getAcquirerConfig(Request $request){
        return null;
    }



}

