<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{

    /* FOR SHOWING ACTIVE INACTIVE STATIONS AND EQUIPMENTS */
    public function index()
    {
        $Stations            = DB::table('station_inventory')->count();
        $Station_inactive    = DB::table('station_inventory')->where('status', '=', false)->count();
        $Equipments          = DB::table('equipment_inventory')->count();
        $Equipments_inactive = DB::table('equipment_inventory')->where('status', '=', false)->count();
        $indraReplaced       = DB::table('cl_indra_rep')->count();

        return Inertia::render('Dashboard', [
            'Stations'            => $Stations,
            'Station_inactive'    => $Station_inactive,
            'Equipments'          => $Equipments,
            'Equipments_inactive' => $Equipments_inactive,
            'indraReplaced'       => $indraReplaced
        ]);

    }
}
