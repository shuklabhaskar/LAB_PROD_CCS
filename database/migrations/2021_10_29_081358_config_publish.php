<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConfigPublish extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_publish', function (Blueprint $table) {
            $table->id('config_publish_id');
            $table->string('equipment_id');
            $table->integer('config_id');
            $table->integer('config_version');
            $table->integer('sent_by');
            $table->boolean('is_published');
            $table->boolean('is_sent')->default(false);
            $table->dateTime('activation_time')->nullable();
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
        //
    }
}
