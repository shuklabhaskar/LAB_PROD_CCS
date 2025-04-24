<?php

namespace App\Http\Controllers\Modules\Configuration;

use App\Http\Controllers\Controller;
use App\Models\Stations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ConfigController extends Controller
{
    function index(){

        $Configs = DB::table('ms_config_type as a')
            ->leftJoin('config_gen as b', 'a.config_id', '=', 'b.config_id')
            ->select('a.config_id', 'a.config_name', DB::raw("max(b.config_version) config_version"))
            ->where('for_config','=',1)
            ->groupBy(['a.config_id', 'a.config_name'])
            ->get();

        $ConfigDetails = DB::table('config_gen')
            ->get();

        return Inertia::render('Configuration/Index', [
            'Configs'       =>$Configs,
            'ConfigDetails' =>$ConfigDetails
        ]);

    }

    function create($id){

        $config_id = $id;

        if ($config_id == 1) {
            /*EQUIPMENTS*/
            DB::table('equipment_inventory')->where('status', '=', 1)->update(['is_generated' => true]);
            $configurations = DB::table('equipment_inventory')
                ->where('status', '=', 1)
                ->get(['eq_inv_id', 'status']);
        }

        else if ($config_id == 2) {
            /*FARE*/
            $configurations = DB::table('fare_table')
                ->join('fare_inventory','fare_inventory.fare_table_id','=','fare_table.fare_table_id')
                ->where('fare_inventory.status','=',true)
                ->orderBy('fare_table_id','ASC')
                ->select([
                    'fare_table.fare_table_id',
                    'fare_table.source_id',
                    'fare_table.destination_id',
                    'fare_table.fare'])
                ->get();
        }

        else if ($config_id == 3) {
            /*STATION*/
            $configurations = DB::table('station_inventory')
                            ->where('status', '=', true)
                            ->get('stn_id');
        }

        else if ($config_id == 4){
            /*PASSES*/
            $configurations = DB::table('pass_inventory')
                ->orderBy('pass_inv_id', 'ASC')
                ->where('status', true)
                ->get();
        }

        else if ($config_id == 6) /* EQUIPMENT BLACKLIST */
        {
            $configurations = DB::table('equipment_blacklist')->select('equipment_id')->get()->tojson();
            DB::table('equipment_blacklist')->update(['is_generated' => true]);
        }

        else if ($config_id == 7) /* CARD BLACKLISTS */ {
            $configurations = 1;
        }

        else if ($config_id == 8) /* TICKET BLACKLISTS */ {
            $configurations = DB::table('acq_param')->get()->toJson();
        }

        $newConfigGenId = DB::table('config_gen')
            ->orderBy('config_gen_id', 'desc')
            ->first('config_gen_id');

        $newConfigVersion = DB::table('config_gen')
            ->where('config_id', '=', $config_id)
            ->orderBy('config_version', 'desc')
            ->first('config_version');

        DB::table('config_gen')
            ->insert([
                'config_gen_id'  => ($newConfigGenId != null) ? $newConfigGenId->config_gen_id + 1 : 1,
                'config_id'      => $config_id,
                'config_version' => ($newConfigVersion != null) ? $newConfigVersion->config_version + 1 : 1,
                'is_generated'   => true,
                'config_data'    => $configurations ,
                'created_by'     => 1,
            ]);

        return redirect('config')->with([
            'status' => true,
            'message' => ' CONFIGURATION  CREATED SUCCESSFULLY.'
        ]);

    }

    function PublishIndex(){

        $EqConfig = DB::table('config_gen')
            ->where('config_id','=',1)
            ->orderBy('config_version','DESC')
            ->get();

        $FareConfig = DB::table('config_gen')
            ->where('config_id','=',2)
            ->orderBy('config_version','DESC')
            ->get();

        $StnConfig = DB::table('config_gen')
            ->where('config_id','=',3)
            ->orderBy('config_version','DESC')
            ->get();

        $PassConfig = DB::table('config_gen')
            ->where('config_id','=',4)
            ->orderBy('config_version','DESC')
            ->get();

        $UserConfig = DB::table('config_gen')
            ->where('config_id','=',5)
            ->orderBy('config_version','DESC')
            ->get();

        $EqBlConfig = DB::table('config_gen')
            ->where('config_id','=',6)
            ->orderBy('config_version','DESC')
            ->get();

        $CardBlConfig = DB::table('config_gen')
            ->where('config_id','=',7)
            ->orderBy('config_version','DESC')
            ->get();

        $Equipments = DB::table('equipment_inventory as ei')
            ->where('is_blacklisted', '=', 0)
            ->join('station_inventory as si', 'si.stn_id', '=', 'ei.stn_id')
            ->join('ms_equipment_type as et', 'et.eq_type_id', '=', 'ei.eq_type_id')
            ->select('ei.*', 'si.stn_name', 'et.eq_type_id', 'et.eq_type_name')
            ->orderBy('si.stn_id','ASC')
            ->get();

        return Inertia::render('Configuration/PublishIndex', [
            'EqConfig'        => $EqConfig,
            'FareConfig'      => $FareConfig,
            'StnConfig'       => $StnConfig,
            'PassConfig'      => $PassConfig,
            'UserConfig'      => $UserConfig,
            'EqBlConfig'      => $EqBlConfig,
            'CardBlConfig'    => $CardBlConfig,
            'Equipments'      => $Equipments,
        ]);

    }

    public function publishCreate(Request $request)
    {
        $data = json_decode($request->getContent());

        /* Equipment Config */

        if ($data->Equipment->isSelected == true) {

            foreach ($data->selected as $key => $eq_id) {

                $eq_version = DB::table('equipment_inventory')
                    ->where('eq_id','=',$eq_id)
                    ->orderBy('eq_inv_id', 'desc')->first('eq_version');

                DB::table('config_publish')->insert([
                    'equipment_id'    => $eq_id,
                    'config_id'       => $data->Equipment->config_id,
                    'config_version'  => $eq_version->eq_version,
                    'sent_by'         => 1,
                    'is_published'    => 1,
                    'activation_time' => $data->activation_time
                ]);

            }

        }

        /* Fare Config */
        if ($data->Fare->isSelected == true) {
            foreach ($data->selected as $key => $eq_id) {

                DB::table('config_publish')->insert([
                    'equipment_id'    => $eq_id,
                    'config_id'       => $data->Fare->config_id,
                    'config_version'  => $data->Fare->version,
                    'sent_by'         => 1,
                    'is_published'    => 1,
                    'activation_time' => $data->activation_time
                ]);

            }
        }

        /* Station Config */
        if ($data->Station->isSelected == true) {

            foreach ($data->selected as $key => $eq_id) {

                DB::table('config_publish')->insert([
                    'equipment_id'    => $eq_id,
                    'config_id'       => $data->Station->config_id,
                    'config_version'  => 1,
                    'sent_by'         => 1,
                    'is_published'    => 1,
                    'activation_time' => $data->activation_time
                ]);

            }
        }

        /* Pass Config */
        if ($data->Pass->isSelected == true) {

            foreach ($data->selected as $key => $eq_id) {

                DB::table('config_publish')->insert([
                    'equipment_id'    => $eq_id,
                    'config_id'       => $data->Pass->config_id,
                    'config_version'  => $data->Pass->version,
                    'sent_by'         => 1,
                    'is_published'    => 1,
                    'activation_time' => $data->activation_time
                ]);

            }

        }

        /* User Config */
        if ($data->User->isSelected == true) {

            foreach ($data->selected as $key => $eq_id) {

                DB::table('config_publish')->insert([
                    'equipment_id'    => $eq_id,
                    'config_id'       => $data->User->config_id,
                    'config_version'  => $data->User->version,
                    'sent_by'         => 1,
                    'is_published'    => 1,
                    'activation_time' => $data->activation_time
                ]);

            }
        }

        /* CARD BLACKLIST */
        if ($data->CardBlacklist->isSelected == true) {

            foreach ($data->selected as $key => $eq_id) {

                DB::table('config_publish')->insert([
                    'equipment_id'    => $eq_id,
                    'config_id'       => $data->CardBlacklist->config_id,
                    'config_version'  => $data->CardBlacklist->version,
                    'sent_by'         => 1,
                    'is_published'    => 1,
                    'activation_time' => $data->activation_time
                ]);

            }
        }

        /* ACQUIRER BLACKLIST */
        if ($data->Acquirer->isSelected == true) {

            foreach ($data->selected as $key => $eq_id) {

                DB::table('config_publish')->insert([
                    'equipment_id'    => $eq_id,
                    'config_id'       => $data->Acquirer->config_id,
                    'config_version'  => 1,
                    'sent_by'         => 1,
                    'is_published'    => 1,
                    'activation_time' => $data->activation_time
                ]);

            }
        }

        $request = null;

        return redirect()
            ->to('publish')
            ->with([
                'status'  => true,
                'message' => 'CONFIGURATION PUBLISHED SUCCESSFULLY.'
            ]);
    }
}
