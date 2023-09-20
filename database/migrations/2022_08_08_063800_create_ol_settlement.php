<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOlSettlement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ol_settlement', function (Blueprint $table) {
            $table->id('ol_settlement_id');
            $table->text('atek_id')->unique(); //AP001<EQ_ID><Time Stamp>
            $table->dateTime('txn_date');
            $table->double('amount');
            $table->text('eq_id');
            $table->text('bank_order_id')->nullable();
            $table->boolean('is_settle')->default(0);
            $table->boolean('is_test')->default(0);
            $table->dateTime('settlement_date')->nullable();
            $table->double('settlement_amt')->nullable();
            $table->double('mdr_amt')->nullable();
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
        Schema::dropIfExists('ol_settlement');
    }
}
