<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MasterPassParameters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_pass_parameters', function (Blueprint $table) {
            $table -> id();
            $table -> text('params');
            $table -> boolean('sjt')->default(0);
            $table -> boolean('rjt')->default(0);
            $table -> boolean('sv')->default(0);
            $table -> boolean('tp')->default(0);
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
}
