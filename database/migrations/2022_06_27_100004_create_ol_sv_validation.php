<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOlSvValidation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ol_sv_validation', function (Blueprint $table) {
            $table->id('sv_val_id');
            $table->text('atek_id')->unique(); //AP001<EQ_ID><Time Stamp>
            $table->dateTime('txn_date');
            $table->text('card_mask_no');
            $table->text('card_hash_no');
            $table->integer('pay_scheme');
            $table->integer('val_type_id');
            $table->double('amt_deducted');
            $table->integer('media_type_id');
            $table->integer('product_id');
            $table->integer('pass_id');
            $table->text('eq_id');
            $table->double('chip_balance');
            $table->integer('stn_id');
            $table->text('merchant_id');
            $table->text('terminal_id');
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
        Schema::dropIfExists('ol_sv_validation');
    }

}
