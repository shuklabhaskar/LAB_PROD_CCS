<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_inventory', function (Blueprint $table) {
            $table->id('user_inv_id');
            $table->integer('user_id');
            $table->text('first_name');
            $table->text('middle_name')->nullable();
            $table->text('last_name');
            $table->text('designation');
            $table->bigInteger('emp_id');
            $table->bigInteger('emp_mobile');
            $table->text('emp_email');
            $table->integer('emp_gender');
            $table->date('emp_dob');
            $table->text('user_login');
            $table->text('user_pwd');
            $table->boolean('status')->default(0);
            $table->integer(    'user_role_id');
            $table->text('user_data')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_test')->default(0);
            $table->integer('created_by');
            $table->integer('modified_by')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_inventory');
    }
}
