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
        Schema::create('cl_pen_accounting', function (Blueprint $table) {
            $table->id('pen_acc_id');
            $table->integer('cl_acc_id');
            $table->dateTime('txn_date');
            $table->bigInteger('engraved_id');
            $table->integer('pen_type_id');
            $table->double('pen_price');
            $table->integer('stn_id');
            $table->integer('media_type_id');
            $table->integer('product_id');
            $table->integer('pass_id');
            $table->timestamp('created_at')->useCurrent();
        });


        $table_name = 'cl_pen_accounting';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('pen_acc_id', 'index_cl_pen_accounting__pen_acc_id');
            $table->index('cl_acc_id', 'index_cl_pen_accounting__cl_acc_id');
            $table->index('stn_id', 'index_cl_pen_accounting__stn_id');
            $table->index('pen_type_id', 'index_cl_pen_accounting__pen_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_pen_accounting');
    }
};
