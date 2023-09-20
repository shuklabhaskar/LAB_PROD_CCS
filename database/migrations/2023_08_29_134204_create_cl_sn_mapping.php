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
        Schema::create('cl_sn_mapping', function (Blueprint $table) {
            $table->id('cl_sn_mapping_id');
            $table->bigInteger('engraved_id')->unique();
            $table->bigInteger('chip_id')->unique();
            $table->dateTime('insert_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_sn_mapping');
    }
};
