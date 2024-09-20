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
        Schema::create('operators_api_privilege', function (Blueprint $table) {
            $table->id('operators_api_prv_id');
            $table->unsignedBigInteger('operator_id');
            $table->unsignedBigInteger('api_permitted');
            $table->boolean('is_activated');
            $table->timestamps();
        });

        $table_name = 'operators_api_privilege';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('operator_id', 'index_operators_api_privilege_operator_id');
            $table->index('api_permitted', 'index_operators_api_privilege_api_permitted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operators_api_privilege');
    }
};
