<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKategoriUsahaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_kategori_usaha', function (Blueprint $table) {
            $table->unsignedSmallInteger("mku_id", true);
            $table->char("mku_kode", 20);
            $table->char("mku_nama", 150);
            $table->boolean("mku_status")->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_kategori_usaha');
    }
}
