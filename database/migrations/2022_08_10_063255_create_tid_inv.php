<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTidInv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tid_inv', function (Blueprint $table) {
            $table->id('tid_inv_id');
            $table->text('emv_tid')->unique();
            $table->text('emv_serial_no')->unique();
            $table->text('emv_box_serial_no')->nullable();
            $table->integer('emv_type_id')->nullable();
            $table->text('acq_id')->nullable();
            $table->integer('eq_type_id')->nullable();
            $table->text('emv_eq_id')->nullable();
            $table->text('emv_eq_ip')->nullable();
            $table->text('eq_id')->nullable();
            $table->integer('eq_dir_id')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_bind')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tid_inv');
    }
}
