<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StationInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('station_inventory', function (Blueprint $table) {
            $table->id('stn_inv_id');
            $table->integer('stn_id');
            $table->text('stn_name');
            $table->text('description');
            $table->integer('company_id');
            $table->boolean('status');
            $table->integer('line_id');
            $table->text('stn_short_name');
            $table->text('stn_national_lang');
            $table->text('stn_regional_lang');
            $table->double('cord_x');
            $table->double('cord_y');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->text('stn_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('station_inventory');
    }
}
