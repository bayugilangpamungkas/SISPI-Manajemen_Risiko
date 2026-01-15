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
            // Kolom untuk menyimpan data template yang diisi Auditor (JSON format)
            $table->text('template_data')->nullable()->after('auditor_id');

            // Kolom untuk menyimpan waktu template dikirim ke Auditee
            $table->timestamp('template_sent_at')->nullable()->after('template_data');
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
            $table->dropColumn(['template_data', 'template_sent_at']);
        });
    }
};
