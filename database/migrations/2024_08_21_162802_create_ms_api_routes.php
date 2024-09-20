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
        Schema::create('ms_api_routes', function (Blueprint $table) {
            $table->id('ms_api_route_id');
            $table->text('api_name');
            $table->text('api_route')->index();
            $table->text('api_description')->nullable();
            $table->integer('api_request_type');
            $table->integer('product_type_id');
            $table->timestamps();
        });

        $table_name = 'ms_api_routes';
        Schema::table($table_name, function (Blueprint $table) {
            $table->index('api_route', 'index_ms_api_routes_api_route');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_api_routes');
    }
};
