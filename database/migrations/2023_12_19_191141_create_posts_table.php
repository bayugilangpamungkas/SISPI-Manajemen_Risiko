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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_unit_kerja')->references('id')->on('unit_kerjas')->constrained('unit_kerjas')->onDelete('cascade');
            $table->string('waktu');
            $table->string('tempat');
            // $table->string('anggota');
            $table->string('jenis');
            $table->string('judul');
            $table->string('deskripsi');
            $table->string('bidang');
            $table->string('tanggungjawab')->nullable();
            // $table->string('dokumen')->nullable();
            // $table->string('templateA')->nullable();
            // $table->string('templateB')->nullable();
            // $table->string('rubrik')->nullable();
            $table->string('hasilReviu')->nullable();
            $table->string('hasilBerita')->nullable();
            $table->string('hasilPengesahan')->nullable();
            $table->string('hasilRubrik')->nullable();
            $table->string('approvalReviuPIC')->nullable();
            // $table->string('commenter')->nullable();
            // $table->string('commentReviu')->nullable();
            $table->string('approvalReviu')->nullable();
            $table->string('approvalBerita')->nullable();
            $table->string('approvalPengesahan')->nullable();
            $table->string('approvalRubrik')->nullable();
            $table->timestamp('approvalReviu_at')->nullable();
            $table->timestamp('approvalBerita_at')->nullable();
            $table->timestamp('approvalPengesahan_at')->nullable();
            $table->timestamp('approvalRubrik_at')->nullable();
            $table->timestamp('hasilReviu_uploaded_at')->nullable();
            $table->timestamp('hasilBerita_uploaded_at')->nullable();
            $table->timestamp('hasilPengesahan_uploaded_at')->nullable();
            $table->timestamp('hasilRubrik_uploaded_at')->nullable();
            $table->string('koreksiReviuPIC')->nullable();
            $table->string('koreksiReviu')->nullable();
            $table->string('koreksiBerita')->nullable();
            $table->string('koreksiPengesahan')->nullable();
            $table->string('koreksiRubrik')->nullable();
            $table->string('laporan_akhir')->nullable();
            $table->string('suratTugas')->nullable();
            $table->string('approveSuratTugas')->nullable();
            $table->timestamp('approveSuratTugas_at')->nullable();
            $table->string('judul_tindak_lanjut')->nullable();
            $table->string('dokumen_tindak_lanjut')->nullable();
            $table->timestamp('tindakLanjut_at')->nullable();
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
        Schema::dropIfExists('posts');
    }
};
