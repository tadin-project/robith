<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsSubKriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_sub_kriteria', function (Blueprint $table) {
            $table->unsignedSmallInteger("msk_id", true);
            $table->char("msk_kode", 2);
            $table->char("msk_nama", 255);
            $table->boolean("msk_status")->nullable()->default(true);
            $table->unsignedSmallInteger("msk_bobot")->nullable()->default(0);
            $table->unsignedSmallInteger("mk_id")->nullable();
            $table->boolean("msk_is_submission")->nullable()->default(false);

            $table->unique(array('mk_id', 'msk_kode'));
            $table->foreign("mk_id", "fk_ms_sub_kriteria_mk_id")->references("mk_id")->on("ms_kriteria")->onDelete("cascade")->onUpdate("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_sub_kriteria');
    }
}
