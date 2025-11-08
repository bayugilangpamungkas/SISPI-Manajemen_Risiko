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
        Schema::create('unit_kerja_documents', function (Blueprint $table) {
            $table->id();
            $table->string('unit_kerja'); // Kolom untuk menyimpan unit kerja (jenis)
            $table->string('dokumen'); // Kolom untuk menyimpan nama dokumen
            $table->timestamp('uploaded_at'); // Kolom untuk menyimpan waktu upload dokumen
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
        Schema::dropIfExists('unit_kerja_documents');
    }
};
