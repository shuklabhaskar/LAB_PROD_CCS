<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FareInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fare_inventory', function (Blueprint $table) {
            $table -> id();
            $table -> integer('fare_table_id')->unique();
            $table -> string('fare_name');
            $table -> string('description')->nullable();
            $table -> boolean('status');
            $table -> double('fare_version');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_date')->nullable();
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
