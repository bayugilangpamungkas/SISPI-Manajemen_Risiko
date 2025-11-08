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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('username',50);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('id_level');
            $table->unsignedBigInteger('id_unit_kerja');
            $table->foreign('id_level')->references('id')->on('levels')->onDelete('cascade');
            $table->foreign('id_unit_kerja')->references('id')->on('unit_kerjas')->onDelete('cascade');
            $table->string('nip');
            $table->json('menu_config')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('tanda_tangan')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
