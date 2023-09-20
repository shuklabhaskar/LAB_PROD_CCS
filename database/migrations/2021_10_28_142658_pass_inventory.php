<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PassInventory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('pass_inventory', function (Blueprint $table) {
            $table->id('pass_inv_id');
            $table->integer('media_type_id');
            $table->integer('product_id');
            $table->integer('pass_id');
            $table->string('pass_name');
            $table->text('description')->nullable();
            $table->integer('company_id');
            $table->boolean('status');
            $table->text('fare_table_id');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            /* PASS PENALTY DETAILS */
            $table->double('same_stn_over_time_limit')->nullable();
            $table->integer('same_stn_over_time_pen')->nullable();
            $table->double('same_stn_over_time_max_pen')->nullable();
            $table->double('other_stn_over_time_limit')->nullable();
            $table->double('other_stn_over_time_pen')->nullable();
            $table->integer('other_stn_over_time_max_pen')->nullable();
            $table->double('over_travel_pen')->nullable();
            $table->double('entry_mismatch_limit')->nullable();
            $table->double('entry_mismatch_same_time_pen')->nullable();
            $table->double('entry_mismatch_no_exit_pen')->nullable();
            $table->integer('exit_mismatch_pen')->nullable();
            $table->boolean('entry_exit_control')->nullable();
            /* PASS UNIT SECTION */
            $table->double('entry_validity_period')->nullable();
            $table->double('return_validity_period')->nullable();
            $table->double('pass_validity_period')->nullable();
            $table->double('grace_period')->nullable();
            $table->string('trip_count')->nullable();
            $table->double('daily_trip_limit')->nullable();
            $table->boolean('reload_permit')->nullable();
            $table->boolean('refund_permit')->nullable();
            $table->double('refund_charges')->nullable();
            /* SV BALANCE SECTION */
            $table->double('min_sv_topup_amt')->nullable();
            $table->double('sv_step_topup_amt')->nullable();
            $table->double('min_sv_entry_bal')->nullable();
            $table->double('max_sv_bal')->nullable();
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_date')->nullable();
            $table->integer('updated_by')->nullable();
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
