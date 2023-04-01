<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKategoriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_kategori', function (Blueprint $table) {
            $table->unsignedSmallInteger("mk_id", true);
            $table->char("mk_kode", 2)->unique();
            $table->char("mk_nama", 255);
            $table->boolean("mk_status")->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_kategori');
    }
}
