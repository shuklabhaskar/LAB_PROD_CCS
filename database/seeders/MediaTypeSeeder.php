<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ms_media_types') -> insert(['media_name' => 'OLC','description'=>'For OLC Ticketing']);
        DB::table('ms_media_types') -> insert(['media_name' => 'CLC','description'=>'For CLC Ticketing']);
        DB::table('ms_media_types') -> insert(['media_name' => 'Mobile','description'=>'For Mobile Ticketing']);
        DB::table('ms_media_types') -> insert(['media_name' => 'Paper','description'=>'For Paper Ticketing']);
        DB::table('ms_media_types') -> insert(['media_name' => 'whatsApp','description'=>'For whatsApp Ticketing']);

    }
}
