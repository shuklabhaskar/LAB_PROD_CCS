<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fare_table', function (Blueprint $table) {
            $table -> id();
            $table -> integer('fare_table_id');
            $table -> integer('source_id');
            $table -> integer('destination_id');
            $table -> integer('fare');
            $table -> integer('fare_version');
            $table->timestamp('created_date')->default(now());
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
