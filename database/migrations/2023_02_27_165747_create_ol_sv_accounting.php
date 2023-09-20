<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ol_sv_accounting', function (Blueprint $table) {
            $table->id('ms_acc_id');
            $table->text('atek_id')->unique(); //AP001<EQ_ID><Time Stamp>
            $table->dateTime('txn_date');
            $table->text('bank_ref_id')->nullable();
            $table->text('card_mask_no');
            $table->text('card_hash_no')->nullable();
            $table->integer('pay_scheme');
            $table->integer('op_type_id');
            $table->integer('stn_id');
            $table->double('cash_col');
            $table->double('cash_ret');
            $table->double('total_price');
            $table->double('pre_chip_bal');
            $table->double('pos_chip_bal');
            $table->integer('media_type_id');
            $table->integer('product_id');
            $table->bigInteger('pass_id');
            $table->bigInteger('shift_id');
            $table->bigInteger('user_id');
            $table->bigInteger('eq_id');
            $table->text('merchant_id');
            $table->text('terminal_id');
            $table->integer('pay_type_id');
            $table->text('pay_ref')->nullable();
            $table->boolean('is_test')->default(0);
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
        Schema::dropIfExists('ol_sv_accounting');
    }
};
