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
        Schema::create('cl_sv_validation', function (Blueprint $table) {
            $table->id('sv_val_id');
            $table->text('atek_id')->unique();
            $table->dateTime('txn_date');
            $table->bigInteger('engraved_id');
            $table->integer('val_type_id');
            $table->double('amt_deducted');
            $table->double('chip_balance');
            $table->integer('media_type_id');
            $table->integer('product_id');
            $table->integer('pass_id');
            $table->integer('card_type_id');
            $table->text('eq_id');
            $table->integer('stn_id');
            $table->boolean('is_test')->default(0)->nullable();
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
        Schema::dropIfExists('cl_sv_validation');
    }
};
