<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('ol_pen_accounting', function (Blueprint $table) {
            $table->id('pen_acc_id');
            $table->integer('ms_acc_id');
            $table->dateTime('txn_date');
            $table->text('card_mask_no');
            $table->text('card_hash_no');
            $table->integer('pay_scheme');
            $table->integer('pen_type_id');
            $table->bigInteger('pen_price');
            $table->bigInteger('stn_id');
            $table->integer('media_type_id');
            $table->integer('product_id');
            $table->bigInteger('pass_id');
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
        //
    }
};
