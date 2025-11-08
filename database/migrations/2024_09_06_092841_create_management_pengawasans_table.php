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
        Schema::create('management_pengawasans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_management_uraian');
            $table->foreign('id_management_uraian')->references('id')->on('uraians')->onDelete('cascade');
            $table->string('kualitas_pengawasan');
            $table->string('aktivitas_pengawasan');
            $table->string('parameter');
            $table->string('sub_parameter');
            $table->string('cara_pengukuran');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('management_pengawasans');
    }
};
