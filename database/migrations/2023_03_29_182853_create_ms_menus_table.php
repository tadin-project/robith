<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_menus', function (Blueprint $table) {
            $table->id('menu_id');
            $table->char('menu_kode', 50)->unique();
            $table->char('menu_nama', 255);
            $table->boolean('menu_status')->nullable()->default(true);
            $table->tinyInteger('menu_type')->nullable()->default(1)->comment("1=Link;\n2=Title");
            $table->char('menu_link', 255)->nullable();
            $table->char('menu_ikon', 255)->nullable();
            $table->bigInteger('parent_menu_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_menus');
    }
}
