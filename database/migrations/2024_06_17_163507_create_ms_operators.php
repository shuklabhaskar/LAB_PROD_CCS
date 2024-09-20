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
        Schema::create('ms_operators', function (Blueprint $table) {
            $table->id('ms_operator_id');
            $table->unsignedBigInteger('operator_id');
            $table->string('operator_name');
            $table->string('user_name');
            $table->string('user_password');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });

        $table_name = 'ms_operators';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('operator_id', 'index_ms_operators__operator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_operators');
    }
};
