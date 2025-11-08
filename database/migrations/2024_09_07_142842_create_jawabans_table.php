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
        Schema::create('jawabans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('id_management_uraian');
            $table->foreign('id_management_uraian')->references('id')->on('uraians')->onDelete('cascade');
            $table->boolean('status')->default(false); 
            $table->string('dokumen')->nullable(); 
            $table->year('tahun')->nullable(); 
            $table->timestamp('validasi_at')->nullable(); // Menyimpan tanggal dan waktu validasi
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jawabans');
    }
};
