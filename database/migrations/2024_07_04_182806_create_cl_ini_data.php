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
        Schema::create('cl_ini_data', function (Blueprint $table) {
            $table->id('cl_ini_data_id');
            $table->dateTime('txn_date');
            $table->bigInteger('engraved_id');
            $table->bigInteger('chip_id');
            $table->text('eq_id');
            $table->timestamp('card_expiry')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });

        $table_name = 'cl_ini_data';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('engraved_id', 'index_cl_ini_data_engraved_id');
            $table->index('chip_id', 'index_cl_ini_data_chip_id');
            $table->index('txn_date', 'index_cl_ini_data_txn_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_ini_data');
    }
};
