<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsDimensiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_dimensi', function (Blueprint $table) {
            $table->unsignedSmallInteger("md_id", true);
            $table->char("md_kode", 2)->unique();
            $table->char("md_nama", 255);
            $table->boolean("md_status")->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_dimensi');
    }
}
