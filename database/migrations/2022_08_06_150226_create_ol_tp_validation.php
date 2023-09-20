<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOlTpValidation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ol_tp_validation', function (Blueprint $table) {
            $table->id('tp_val_id');
            $table->dateTime('txn_date');
            $table->text('card_mask_no');
            $table->text('card_hash_no');
            $table->integer('card_type');
            $table->integer('pay_scheme');
            $table->integer('val_type_id');
            $table->double('trip_deducted');
            $table->bigInteger('remaining_trips');
            $table->dateTime('pass_expiry_date');
            $table->integer('media_type_id');
            $table->integer('product_id');
            $table->integer('pass_id');
            $table->text('eq_id');
            $table->double('chip_balance');
            $table->integer('src_stn_id');
            $table->integer('des_stn_id');
            $table->text('terminal_id');
            $table->text('trace_number');
            $table->text('batch_id');
            $table->boolean('is_test');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ol_tp_validation');
    }
}
