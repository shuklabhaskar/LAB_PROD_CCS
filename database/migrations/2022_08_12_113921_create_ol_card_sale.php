<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOlCardSale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ol_card_sale', function (Blueprint $table) {
            $table->id('ol_card_sale_id');
            $table->text('atek_id')->unique();
            $table->dateTime('txn_date');
            $table->text('bank_ref_id');
            $table->text('card_id');
            $table->text('card_mask_no');
            $table->text('card_hash_no');
            $table->integer('card_type');
            $table->integer('pay_scheme');
            $table->integer('op_type_id');
            $table->integer('stn_id');
            $table->float('cash_col');
            $table->float('cash_ret');
            $table->float('total_price');
            $table->float('card_fee');
            $table->float('card_sec');
            $table->float('initial_topup_amt');
            $table->text('pax_first_name')->nullable();
            $table->text('pax_middle_name')->nullable();
            $table->text('pax_last_name')->nullable();
            $table->bigInteger('pax_mobile')->unique();
            $table->integer('pax_gen_type')->nullable();
            $table->text('shift_id');
            $table->text('user_id');
            $table->text('eq_id');
            $table->integer('pay_type_id');
            $table->text('pay_ref')->nullable();
            $table->integer('bank_id');
            $table->boolean('is_test')->default(0);
            $table->integer('card_iss_status');
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
        Schema::dropIfExists('ol_card_sale');
    }
}
