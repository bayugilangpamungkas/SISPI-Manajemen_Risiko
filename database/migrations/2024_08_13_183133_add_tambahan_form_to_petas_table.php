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
            $table->string('kode_regist');
            
            $table->text('pernyataan');
            $table->text('kategori');
            $table->text('uraian');
            $table->text('metode');
            $table->integer('skor_kemungkinan');
            $table->integer('skor_dampak');
            $table->string('tingkat_risiko')->nullable();
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
