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
        Schema::create('rtm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_post')->constrained('posts');
            $table->text('temuan')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->text('rencanaTinJut')->nullable();
            $table->date('rencanaWaktuTinJut')->nullable();
            $table->enum('status_rtm', ['Open', 'In Progress', 'Closed'])->default('Open');
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
        Schema::dropIfExists('rtm');
    }
};
