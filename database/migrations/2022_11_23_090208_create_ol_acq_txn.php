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
        Schema::create('ol_acq_txn', function (Blueprint $table) {
            $table->id('ol_acq_id');
            $table->text('atek_id')->unique(); //AP001<EQ_ID><Time Stamp>
            $table->dateTime('txn_date');
            $table->text('request_json');
            $table->integer('stn_id');
            $table->text('eq_id');
            $table->text('emv_tid');
            $table->boolean('is_sync')->default(0);
            $table->boolean('is_test')->default(0);
            $table->text('response_json')->nullable();
            $table->text('bank_order_id')->nullable();
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
        Schema::dropIfExists('ol_acq_txn');
    }
};
