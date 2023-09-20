<?php

namespace App\Http\Controllers\Modules\CrashReport;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/* CONTROLLER MADE FOR VIEWING AND ALL LOGS OF AG,TOM AND EDC RESPECTIVELY IN ACCORDATION MANNER */
class ViewCrashReport extends Controller
{
    /*SHOW DATA FOR ACCORDATION*/
    public function index()
    {

        $gate_logs = DB::table('log_gate')
            ->get();

        $tom_logs = DB::table('log_tom')
            ->get();

        $edc_logs = DB::table('log_edc')
            ->get();

        return Inertia::render('CrashReport/Index', [
            'gate_logs' => $gate_logs,
            'tom_logs' => $tom_logs,
            'edc_logs' => $edc_logs,
        ]);
    }

    /*VIEW GATE LOGS*/
    public function viewGateLog($id)
    {

        $gate_log = DB::table('log_gate')
            ->where('gate_log_id', '=', $id)->first('log');

        return Inertia::render('CrashReport/ViewGateLog', [
            'gate_log' => $gate_log->log,
        ]);

    }

    /*VIEW TOM LOGS*/
    public function viewTomLog($id)
    {

        $tom_log = DB::table('log_tom')
            ->where('tom_log_id', '=', $id)->first('log');


        return Inertia::render('CrashReport/ViewTomLog', [
            'tom_log' => $tom_log->log,
        ]);

    }

    /*VIEW EDC LOGS*/
    public function viewEdcLog($id)
    {

        $edc_log = DB::table('log_edc')
            ->where('edc_log_id', '=', $id)->first('log');

        return Inertia::render('CrashReport/ViewEdcLog', [
            'edc_log' => $edc_log->log,
        ]);

    }
}
