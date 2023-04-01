<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsSubKategoriTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_sub_kategori', function (Blueprint $table) {
            $table->unsignedSmallInteger("msk_id", true);
            $table->char("msk_kode", 2)->unique();
            $table->char("msk_nama", 255);
            $table->boolean("msk_status")->nullable()->default(true);
            $table->unsignedSmallInteger("mk_id")->nullable();

            $table->foreign("mk_id", "fk_ms_sub_kategori_mk_id")->references("mk_id")->on("ms_kategori")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_sub_kategori');
    }
}
