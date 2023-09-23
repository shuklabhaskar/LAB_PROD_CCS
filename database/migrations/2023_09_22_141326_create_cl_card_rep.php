# Author: Bhaskar Shukla
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
        Schema::create('cl_card_rep', function (Blueprint $table) {
            $table->id('cl_indra_id')->unique();
            $table->text('atek_id')->unique();
            $table->datetime('txn_date');
            $table->bigInteger('engraved_id');
            $table->bigInteger('chip_id');
            $table->integer('stn_id');
            $table->double('sv_balance')->nullable();
            $table->double('tp_balance')->nullable();
            $table->double('card_sec')->nullable();
            $table->double('card_fee')->nullable();
            $table->integer('pass_id')->nullable();
            $table->integer('product_id');
            $table->dateTime('pass_expiry');
            $table->integer('src_stn_id')->nullable();
            $table->integer('des_stn_id')->nullable();
            $table->text('tid');
            $table->text('eq_id');
            $table->integer('eq_type_id');
            $table->text('pax_first_name')->nullable();
            $table->text('pax_last_name')->nullable();
            $table->integer('pax_mobile')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        $table_name = 'cl_card_rep';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('atek_id', 'index_cl_card_rep__atek_id');
            $table->index('engraved_id', 'index_cl_card_rep__engraved_id');
            $table->index('chip_id', 'index_cl_card_rep__chip_id');
            $table->index('pax_mobile', 'index_cl_card_rep__pax_mobile');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cl_card_rep');
    }
};
