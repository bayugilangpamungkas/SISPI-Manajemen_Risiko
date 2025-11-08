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
            // $table->dropColumn('kode');
            // $table->dropColumn('iku');
            // $table->dropColumn('sasaran');
            // $table->dropColumn('proker');
            // $table->dropColumn('indikator');
            // $table->dropColumn('anggaran');
            // $table->dropColumn('pernyataan');
            // $table->dropColumn('kategori');
            // $table->dropColumn('uraian');
            // $table->dropColumn('metode');
            // $table->dropColumn('skor_kemungkinan');
            // $table->dropColumn('skor_dampak');
            // $table->dropColumn('tingkat_risiko');
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
            //
        });
    }
};
