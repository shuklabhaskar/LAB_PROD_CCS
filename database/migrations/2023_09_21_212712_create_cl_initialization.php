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
        Schema::create('cl_initialization', function (Blueprint $table) {
            $table->id('cl_ini_id');
            $table->dateTime('txn_date');
            $table->bigInteger('engraved_id');
            $table->bigInteger('chip_id');
            $table->text('eq_id');
            $table->integer('eq_type_id');
            $table->integer('stn_id');
            $table->integer('shift_id');
            $table->integer('user_id');
            $table->timestamp('created_at')->useCurrent();
        });

        $table_name = 'cl_initialization';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('engraved_id', 'index_cl_initialization__engraved_id');
            $table->index('chip_id', 'index_cl_initialization__chip_id');
            $table->index('eq_id', 'index_cl_initialization__eq_id');
            $table->index('stn_id', 'index_cl_initialization__stn_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_initialization');
    }
};
