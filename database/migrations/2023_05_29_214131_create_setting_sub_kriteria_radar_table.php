<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingSubKriteriaRadarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_sub_kriteria_radar', function (Blueprint $table) {
            $table->id('sskr_id');
            $table->unsignedSmallInteger('mr_id');
            $table->unsignedSmallInteger('msk_id');

            $table->foreign("msk_id", "fk_setting_sub_kriteria_radar_msk_id")->references("msk_id")->on("ms_sub_kriteria")->onDelete("restrict")->onUpdate("restrict");
            $table->foreign("mr_id", "fk_setting_sub_kriteria_radar_mr_id")->references("mr_id")->on("ms_radar")->onDelete("restrict")->onUpdate("restrict");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_sub_kriteria_radar');
    }
}
