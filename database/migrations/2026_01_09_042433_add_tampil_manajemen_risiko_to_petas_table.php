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
            $table->boolean('tampil_manajemen_risiko')->default(0)->after('status_telaah')->comment('Status tampil di halaman Manajemen Risiko (0=hidden, 1=shown)');
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
            $table->dropColumn('tampil_manajemen_risiko');
        });
    }
};
