<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_menus', function (Blueprint $table) {
            $table->id('gm_id');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedSmallInteger('group_id');

            $table->foreign('menu_id', 'fk_gm_menu_id')->references('menu_id')->on('ms_menus')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('group_id', 'fk_gm_group_id')->references('group_id')->on('ms_groups')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_menus');
    }
}
