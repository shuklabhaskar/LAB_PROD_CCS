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
        Schema::create('firmware_publish', function (Blueprint $table) {
            $table->id('firmware_publish_id');
            $table->integer('firmware_upload_id');
            $table->text('equipment_id');
            $table->integer('firmware_id');
            $table->text('firmware_version');
            $table->string('description')->nullable();
            $table->integer('sent_by');
            $table->boolean('is_published');
            $table->boolean('is_sent')->default(false);
            $table->dateTime('activation_time')->nullable();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('firmware_publish');
    }
};
