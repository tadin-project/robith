<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnFromAsesmenDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('asesmen_detail', function (Blueprint $table) {
            $table->dropForeign('asesmen_detail_msk_id_fk');
            $table->dropColumn('msk_id');
            $table->dropColumn('asd_file');
            $table->unsignedBigInteger("sskr_id");
            $table->integer("asd_final")->nullable();

            $table->foreign("sskr_id", "asesmen_detail_sskr_id_fk")->references("sskr_id")->on("setting_sub_kriteria_radar")->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('asesmen_detail', function (Blueprint $table) {
            $table->unsignedSmallInteger("msk_id");
            $table->text("asd_file")->nullable()->comment("nama file di direktori");
            $table->foreign("msk_id", "asesmen_detail_msk_id_fk")->references("msk_id")->on("ms_sub_kriteria")->onUpdate("cascade")->onDelete("cascade");

            $table->dropForeign('asesmen_detail_sskr_id_fk');
            $table->dropColumn('sskr_id');
            $table->dropColumn('asd_final');
        });
    }
}
