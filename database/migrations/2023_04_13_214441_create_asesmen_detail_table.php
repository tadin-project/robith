<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsesmenDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asesmen_detail', function (Blueprint $table) {
            $table->id("asd_id");
            $table->unsignedBigInteger("as_id");
            $table->unsignedSmallInteger("msk_id");
            $table->integer("asd_value")->nullable()->default(0);
            $table->text("asd_file")->nullable()->comment("nama file di direktori");
            $table->smallInteger("asd_status")->nullable()->default(0)->comment("Untuk centangan apakah inputan ini sudah valid datanya\n0=belum divalidasi;\n1=sudah valid;\n2=ditolak");
            $table->text("asd_comment")->nullable()->comment("Catatan kriteria jika ditolak");

            $table->foreign("as_id", "asesmen_detail_as_id_fk")->references("as_id")->on("asesmen")->onUpdate("cascade")->onDelete("cascade");
            $table->foreign("msk_id", "asesmen_detail_msk_id_fk")->references("msk_id")->on("ms_sub_kriteria")->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asesmen_detail');
    }
}
