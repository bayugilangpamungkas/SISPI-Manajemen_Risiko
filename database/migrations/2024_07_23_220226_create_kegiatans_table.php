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
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_unit_kerja');
            $table->string('id_kegiatan');
            $table->string('judul');
            $table->string('iku');
            $table->string('sasaran');
            $table->string('proker');
            $table->string('indikator');
            $table->string('anggaran');
            $table->foreign('id_unit_kerja')->references('id')->on('unit_kerjas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kegiatans');
    }
};
