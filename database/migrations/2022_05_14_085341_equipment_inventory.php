<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EquipmentInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_inventory', function (Blueprint $table) {
            /* EQUIPMENT BASIC DETAILS*/
            $table->id('eq_inv_id');
            $table->text('atek_eq_id');
            $table->integer('eq_type_id');
            $table->integer('eq_mode_id')->nullable();
            $table->integer('eq_role_id')->nullable();
            $table->integer('eq_num');
            $table->integer('stn_id');
            $table->text('eq_id')->unique();
            $table->integer('eq_location_id');
            $table->text('description');
            $table->longText('cord_x');
            $table->longText('cord_y');
            $table->boolean('status');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            /*NETWORK DETAILS*/
            $table->text('ip_address');
            $table->text('primary_ssid');
            $table->text('primary_ssid_pwd');
            $table->text('backup_ssid')->nullable();
            $table->text('backup_ssid_pwd')->nullable();
            $table->text('gateway');
            $table->text('subnetmask');
            /*EQUIPMENT DATE AND DATA DETAILS*/
            $table->boolean('is_generated')->default(0);
            $table->boolean('is_blacklisted')->default(0);
            $table->dateTime('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_date')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('eq_version')->default(1);
            $table->text('eq_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
