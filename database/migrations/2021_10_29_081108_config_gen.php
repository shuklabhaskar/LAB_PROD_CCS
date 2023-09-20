<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConfigGen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_gen', function (Blueprint $table) {
            $table->id('config_gen_id');
            $table->integer('config_id');
            $table->integer('config_version');
            $table->dateTime('record_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('description')->nullable();
            $table->boolean('is_generated');
            $table->json('config_data');
            $table->integer('created_by')->nullable();
            $table->dateTime('start_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('end_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
