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
            if (!Schema::hasColumn('petas', 'catatan_revisi')) {
                $table->text('catatan_revisi')->nullable()->after('status_konfirmasi_auditor')->comment('JSON catatan revisi dari auditor ke auditee');
            }
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
            $table->dropColumn('catatan_revisi');
        });
    }
};
