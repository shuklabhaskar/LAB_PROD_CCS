<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAcqParam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acq_param', function (Blueprint $table) {
            $table->id('acq_param_id');
            $table->text('operator_id');
            $table->text('eq_type_id')->nullable();
            $table->text('acq_id');
            $table->text('acq_name');
            $table->text('acq_mid');
            $table->text('client_id')->unique();
            $table->integer('line_id');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('acq_param');
    }
}
