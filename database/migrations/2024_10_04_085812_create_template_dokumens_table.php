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
        Schema::create('template_dokumens', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->foreignId('id_jenis')->references('id')->on('jenis_kegiatan')->constrained('jenis_kegiatan')->onDelete('cascade');
            $table->string('dokumen');
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
        Schema::dropIfExists('template_dokumens');
    }
};
