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
        Schema::table('petas', function (Blueprint $table) {
            // Kolom untuk penugasan auditor
            $table->unsignedBigInteger('auditor_id')->nullable()->after('waktu_telaah_spi');
            $table->foreign('auditor_id')->references('id')->on('users')->onDelete('set null');

            // Kolom untuk laporan per unit (untuk unit kerja)
            $table->string('laporan_unit')->nullable()->after('auditor_id');

            // Kolom untuk laporan SPI (untuk internal SPI)
            $table->string('laporan_spi')->nullable()->after('laporan_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('petas', function (Blueprint $table) {
            $table->dropForeign(['auditor_id']);
            $table->dropColumn(['auditor_id', 'laporan_unit', 'laporan_spi']);
        });
    }
};
