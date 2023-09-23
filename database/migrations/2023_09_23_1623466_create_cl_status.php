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
            $table->double('card_fee')->default(0);
            $table->double('card_sec')->default(0);
            $table->double('sv_balance')->default(0);
            $table->double('tp_balance')->default(0);
            $table->dateTime('pass_expiry');
            $table->integer('src_stn_id')->default(0);
            $table->integer('des_stn_id')->default(0);
            $table->boolean('auto_topup_status')->nullable();
            $table->double('auto_topup_amt')->default(0);
            $table->double('bonus_points')->default(0);
            $table->boolean('is_test')->default(false);
            $table->text('pax_first_name')->default("");
            $table->text('pax_last_name')->default("");
            $table->bigInteger('pax_mobile')->default(0);
            $table->integer('pax_gen_type')->default(0);
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
