<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoginInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_info', function (Blueprint $table) {
            $table->id('user_login_info_id');
            $table->integer('user_id');
            $table->integer('shift_id');
            $table->integer('eq_id');
            $table->dateTime('login_date')->nullable();
            $table->dateTime('logout_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_login_info');
    }
}
