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
        Schema::create('cl_sv_accounting', function (Blueprint $table) {
            $table->id('cl_acc_id')->unique();
            $table->text('atek_id')->unique();
            $table->datetime('txn_date');
            $table->bigInteger('engraved_id');
            $table->integer('op_type_id');
            $table->integer('stn_id');
            $table->double('cash_col')->default(0.0);
            $table->double('cash_ret')->default(0.0);
            $table->double('pass_price')->default(0.0);
            $table->double('card_fee')->default(0.0);
            $table->double('card_sec')->default(0.0);
            $table->double('processing_fee')->default(0.0);
            $table->double('total_price')->default(0.0);
            $table->double('pass_ref_chr')->default(0.0);
            $table->double('card_fee_ref_chr')->default(0.0);
            $table->double('card_sec_ref_chr')->default(0.0);
            $table->double('pre_chip_bal')->default(0.0);
            $table->double('pos_chip_bal')->default(0.0);
            $table->integer('media_type_id');
            $table->integer('product_id');
            $table->integer('pass_id');
            $table->datetime('pass_expiry');
            $table->text('pax_first_name');
            $table->text('pax_last_name')->default('0');;
            $table->bigInteger('pax_mobile');
            $table->integer('pax_gen_type');
            $table->integer('shift_id');
            $table->integer('user_id');
            $table->text('eq_id');
            $table->integer('pay_type_id');
            $table->text('pay_ref')->default(0);
            $table->boolean('is_test')->default(false);
            $table->bigInteger('old_engraved_id')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });

        $table_name = 'cl_sv_accounting';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('atek_id', 'index_cl_sv_accounting__atek_id');
            $table->index('engraved_id', 'index_cl_sv_accounting__engraved_id');
            $table->index('pax_mobile', 'index_cl_sv_accounting__pax_mobile');
            $table->index('pass_id', 'index_cl_sv_accounting__pass_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_sv_accounting');
    }
};
