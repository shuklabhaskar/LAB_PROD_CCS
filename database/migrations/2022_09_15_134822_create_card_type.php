<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCardType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_type', function (Blueprint $table) {
            $table->id('card_type_id');
            $table->integer('card_id');
            $table->integer('media_type_id')->nullable();
            $table->text('card_name');
            $table->text('description')->nullable();
            $table->text('card_pro_id')->unique();
            $table->float('card_fee');
            $table->float('card_sec');
            $table->boolean('card_sec_refund_permit');
            $table->float('card_sec_refund_charges');
            $table->integer('sv_pass_id')->nullable();
            $table->integer('tp_pass_id')->nullable();
            $table->boolean('status')->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_type');
    }
}
