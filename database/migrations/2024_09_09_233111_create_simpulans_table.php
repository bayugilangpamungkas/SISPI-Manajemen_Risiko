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
        Schema::create('simpulans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_management_topic');
            $table->foreign('id_management_topic')->references('id')->on('management_topics')->onDelete('cascade');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->year('tahun')->nullable();
            $table->text('simpulan')->nullable();
            $table->text('improvement')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('simpulans');
    }
};
