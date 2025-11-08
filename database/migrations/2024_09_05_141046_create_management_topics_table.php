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
        Schema::create('management_topics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_management_sub_element');
            $table->foreign('id_management_sub_element')->references('id')->on('management_sub_elements')->onDelete('cascade');
            $table->string('topik');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('management_topics');
    }
};
