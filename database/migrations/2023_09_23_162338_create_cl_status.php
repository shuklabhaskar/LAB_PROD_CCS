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
        Schema::create('cl_status', function (Blueprint $table) {
            $table->id('cl_status_id')->unique();
            $table->bigInteger('engraved_id');
            $table->bigInteger('chip_id');
            $table->dateTime('txn_date');
            $table->integer('pass_id');
            $table->integer('product_id');
            $table->double('card_fee')->nullable();
            $table->double('card_sec')->nullable();
            $table->double('sv_balance')->nullable();
            $table->double('tp_balance')->nullable();
            $table->dateTime('pass_expiry');
            $table->integer('src_stn_id')->nullable();
            $table->integer('des_stn_id')->nullable();
            $table->boolean('auto_topup_status')->nullable();
            $table->double('auto_topup_amt')->nullable();
            $table->double('bonus_points')->nullable();
            $table->boolean('is_test')->nullable();
            $table->text('pax_first_name')->nullable();
            $table->text('pax_last_name')->nullable();
            $table->bigInteger('pax_mobile')->nullable();
            $table->integer('pax_gen_type')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        $table_name = 'cl_status';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('engraved_id', 'index_cl_status__engraved_id');
            $table->index('pax_mobile', 'index_cl_status__pax_mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cl_status');
    }
};
