<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comment_prs', function (Blueprint $table) {
            // MySQL requires dropping and recreating enum columns
            DB::statement("ALTER TABLE comment_prs MODIFY COLUMN jenis ENUM('keuangan', 'analisis', 'mitigasi') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comment_prs', function (Blueprint $table) {
            // Rollback to original enum values
            DB::statement("ALTER TABLE comment_prs MODIFY COLUMN jenis ENUM('keuangan', 'analisis') NOT NULL");
        });
    }
};
