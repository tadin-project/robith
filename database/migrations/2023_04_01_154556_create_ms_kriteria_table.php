<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_kriteria', function (Blueprint $table) {
            $table->unsignedSmallInteger("mk_id", true);
            $table->char("mk_kode", 2);
            $table->char("mk_nama", 255);
            $table->boolean("mk_status")->nullable()->default(true);
            $table->unsignedSmallInteger("md_id")->nullable();

            $table->unique(["md_id", "mk_kode"]);
            $table->foreign("md_id", "fk_ms_kriteria_md_id")->references("md_id")->on("ms_dimensi")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_kriteria');
    }
}
