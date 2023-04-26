<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsLampiranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_lampiran', function (Blueprint $table) {
            $table->id("lampiran_id");
            $table->char("lampiran_kode");
            $table->char("lampiran_nama");
            $table->smallInteger("lampiran_jenis")->comment("1=File;2=Link;")->nullable()->default(1);
            $table->text("lampiran_field")->nullable();
            $table->boolean("lampiran_status")->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_lampiran');
    }
}
