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
            $table->text('pengendalian')->nullable();
            $table->text('mitigasi')->nullable();
            $table->string('status_konfirmasi_auditee')->nullable();
            $table->string('status_konfirmasi_auditor')->nullable();
            $table->string('file_lampiran')->nullable();
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
            $table->dropColumn(['pengendalian', 'mitigasi', 'status_konfirmasi_auditee', 'status_konfirmasi_auditor', 'file_lampiran']);
        });
    }
};
