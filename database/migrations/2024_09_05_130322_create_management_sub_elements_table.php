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
        Schema::create('management_sub_elements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_management_element');
            $table->foreign('id_management_element')->references('id')->on('management_elements')->onDelete('cascade');
            $table->string('sub_elemen');
            $table->integer('bobot_sub_elemen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('management_sub_elements');
    }
};
