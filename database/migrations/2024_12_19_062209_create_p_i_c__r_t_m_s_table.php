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
        Schema::create('pic_rtm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_rtm')->constrained('rtm');
            $table->foreignId('id_unit_kerja')->constrained('unit_kerjas');
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
        Schema::dropIfExists('pic_rtm');
    }
};
