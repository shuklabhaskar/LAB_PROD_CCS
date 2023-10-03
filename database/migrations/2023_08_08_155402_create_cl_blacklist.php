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
    public function up(): void
    {
        Schema::create('cl_blacklist', function (Blueprint $table) {
            $table->id('cl_blk_id');
            $table->integer('ms_blk_reason_id');
            $table->dateTime('start_date');
            $table->bigInteger('engraved_id')->unique();
            $table->bigInteger('chip_id')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cl_blacklist');
    }
};
