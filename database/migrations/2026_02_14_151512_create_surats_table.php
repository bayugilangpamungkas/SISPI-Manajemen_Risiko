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
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('jenis_surat');
            $table->string('tujuan_surat');
            $table->string('perihal');
            $table->string('lampiran')->nullable();
            $table->text('isi_surat');
            $table->date('tanggal_surat');

            // Referensi opsional
            $table->enum('tipe_referensi', ['Tanpa Referensi', 'Peta Risiko', 'Audit'])->default('Tanpa Referensi');
            $table->unsignedBigInteger('referensi_id')->nullable();

            // File PDF
            $table->string('file_pdf')->nullable();

            // Status
            $table->enum('status', ['Draft', 'Final'])->default('Draft');

            // Admin yang membuat
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('surats');
    }
};
