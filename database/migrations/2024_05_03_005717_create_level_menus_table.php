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
        Schema::create('level_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_level');
            $table->foreign('id_level')->references('id')->on('levels')->onDelete('cascade');
            $table->unsignedBigInteger('id_menu');
            $table->foreign('id_menu')->references('id')->on('menus')->onDelete('cascade');
            

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
        Schema::dropIfExists('level_menus');
    }
};
