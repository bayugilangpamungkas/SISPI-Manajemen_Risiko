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
        Schema::create('hasil_audit', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->unsignedBigInteger('peta_id');
            $table->unsignedBigInteger('auditor_id');
            
            // Audit Comments
            $table->text('komentar_1');
            $table->text('komentar_2');
            $table->text('komentar_3');
            
            // Control and Mitigation
            $table->text('pengendalian');
            $table->string('mitigasi');
            
            // Status Confirmations (stored as string: disetujui, ditolak, perlu_revisi)
            $table->string('status_konfirmasi_auditee')->nullable();
            $table->string('status_konfirmasi_auditor')->nullable();
            
            // Risk Information
            $table->string('unit_kerja');
            $table->string('kode_risiko')->nullable();
            $table->string('kegiatan');
            $table->string('level_risiko'); // HIGH, MODERATE, LOW
            $table->string('risiko_residual'); // Extreme, High, Moderate, Low
            $table->integer('skor_total')->nullable();
            
            // Year and Auditor Info
            $table->string('tahun_anggaran', 4);
            $table->string('nama_pemonev');
            $table->string('nip_pemonev')->nullable();
            
            // File attachment (optional)
            $table->string('file_lampiran')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('peta_id');
            $table->index('auditor_id');
            $table->index('tahun_anggaran');
            $table->index('unit_kerja');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hasil_audit');
    }
};
