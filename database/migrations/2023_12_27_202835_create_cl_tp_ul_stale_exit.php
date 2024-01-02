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
        Schema::create('cl_tp_ul_stale_exit', function (Blueprint $table) {
            $table->id('cl_tp_ul_stale_exit_id');
            $table->date('date')->unique();
            $table->double('distribution_amount')->default(0);
            $table->double('stale_amount')->default(0);
        });

        $table_name = 'cl_tp_ul_stale_exit';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('date', 'index_cl_tp_ul_stale_exit__date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_tp_ul_stale_exit');
    }
};
