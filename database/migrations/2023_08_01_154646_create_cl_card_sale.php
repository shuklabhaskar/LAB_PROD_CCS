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
        Schema::create('cl_card_sale', function (Blueprint $table) {
		    $table->id('cl_card_sale_id');
		    $table->text('atek_id')->unique();
		    $table->dateTime('txn_date');
		    $table->bigInteger('engraved_id');
		    $table->integer('op_type_id');
		    $table->integer('stn_id');
		    $table->double('total_price');
		    $table->double('card_fee');
		    $table->double('card_sec');
		    $table->text('pax_first_name');
		    $table->text('pax_last_name')->nullable();
		    $table->bigInteger('pax_mobile');
		    $table->integer('pax_gen_type');
		    $table->integer('shift_id');
		    $table->integer('user_id');
		    $table->text('eq_id');
		    $table->integer('pay_type_id');
		    $table->text('pay_ref')->nullable();
		    $table->boolean('is_test')->default(0);
		    $table->integer('media_type_id');
		    $table->integer('card_type_id');
		    $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
		});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_card_sale');
    }
};
