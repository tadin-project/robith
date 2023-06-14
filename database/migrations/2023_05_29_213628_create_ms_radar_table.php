<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsRadarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_radar', function (Blueprint $table) {
            $table->unsignedSmallInteger('mr_id', true);
            $table->char('mr_kode', 20);
            $table->char('mr_nama', 255);
            $table->char('mr_color', 255)->nullable()->default("#076AB4");
            $table->mediumInteger('mr_bobot')->nullable()->default(0);
            $table->boolean('mr_status')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_radar');
    }
}
