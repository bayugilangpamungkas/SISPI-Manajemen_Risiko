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
        Schema::create('petas', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('id_kegiatan');
            // $table->string('nama');
            $table->string('judul');
            $table->string('jenis'); //unit kerja
            $table->string('anggaran');
            // $table->string('nama');
            $table->string('dokumen')->nullable();
            $table->timestamp('dokumen_at')->nullable();
            $table->string('waktu')->nullable();
            $table->string('anggota')->nullable();
            $table->string('approvalPr')->nullable();
            $table->timestamp('approvalPr_at')->nullable();
            $table->string('koreksiPr')->nullable();
            $table->timestamp('koreksiPr_at')->nullable();
            $table->string('status_telaah')->nullable();
            $table->date('waktu_telaah_subtansi')->nullable();
            $table->date('waktu_telaah_teknis')->nullable();
            $table->date('waktu_telaah_spi')->nullable();
            // $table->foreign('id_kegiatan')->references('id')->on('kegiatans')->onDelete('cascade');

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
        Schema::dropIfExists('petas');
    }
};
