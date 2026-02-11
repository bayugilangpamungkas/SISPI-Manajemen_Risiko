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
        // Tambahkan kolom ke tabel petas untuk audit wawancara
        Schema::table('petas', function (Blueprint $table) {
            // Cek kolom satu per satu agar tidak error jika sudah ada
            if (!Schema::hasColumn('petas', 'template_data')) {
                $table->text('template_data')->nullable()->after('auditor_id')->comment('JSON daftar pertanyaan dari auditor');
            }
            if (!Schema::hasColumn('petas', 'template_sent_at')) {
                $table->timestamp('template_sent_at')->nullable()->after('template_data')->comment('Waktu kirim template pertanyaan');
            }
            if (!Schema::hasColumn('petas', 'auditee_response')) {
                $table->text('auditee_response')->nullable()->after('template_sent_at')->comment('JSON jawaban dari auditee');
            }
            if (!Schema::hasColumn('petas', 'status_konfirmasi_auditee')) {
                $table->string('status_konfirmasi_auditee', 50)->nullable()->after('auditee_response')->comment('Status konfirmasi auditee: confirmed, dll');
            }
            if (!Schema::hasColumn('petas', 'status_konfirmasi_auditor')) {
                $table->string('status_konfirmasi_auditor', 50)->nullable()->after('status_konfirmasi_auditee')->comment('Status konfirmasi auditor: reviewed, dll');
            }
        });

        // Tambahkan kolom ke tabel hasil_audit untuk menyimpan penilaian per pertanyaan
        if (Schema::hasTable('hasil_audit')) {
            Schema::table('hasil_audit', function (Blueprint $table) {
                if (!Schema::hasColumn('hasil_audit', 'penilaian_data')) {
                    $table->text('penilaian_data')->nullable()->after('nip_pemonev')->comment('JSON penilaian auditor per pertanyaan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Hapus kolom dari tabel petas
        Schema::table('petas', function (Blueprint $table) {
            $table->dropColumn([
                'template_data',
                'template_sent_at',
                'auditee_response',
                'status_konfirmasi_auditee',
                'status_konfirmasi_auditor',
            ]);
        });

        // Hapus kolom dari tabel hasil_audit
        if (Schema::hasTable('hasil_audit')) {
            Schema::table('hasil_audit', function (Blueprint $table) {
                $table->dropColumn('penilaian_data');
            });
        }
    }
};
