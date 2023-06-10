<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsIntroductionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_introduction', function (Blueprint $table) {
            $table->unsignedMediumInteger("mi_id", true);
            $table->char("mi_kode", 255);
            $table->char("mi_nama", 255);
            $table->text("mi_isi")->default("");
            $table->boolean("mi_status")->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_introduction');
    }
}
