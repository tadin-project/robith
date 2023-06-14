<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsesmenFileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asesmen_file', function (Blueprint $table) {
            $table->id("asf_id");
            $table->unsignedBigInteger("as_id");
            $table->unsignedSmallInteger("msk_id");
            $table->text("asf_file")->nullable()->comment("nama file di direktori");

            $table->foreign("as_id", "asesmen_file_as_id_fk")->references("as_id")->on("asesmen")->onUpdate("cascade")->onDelete("cascade");
            $table->foreign("msk_id", "asesmen_file_msk_id_fk")->references("msk_id")->on("ms_sub_kriteria")->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asesmen_file');
    }
}
