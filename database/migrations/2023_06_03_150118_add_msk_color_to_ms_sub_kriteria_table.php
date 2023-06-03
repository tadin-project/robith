<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMskColorToMsSubKriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ms_sub_kriteria', function (Blueprint $table) {
            $table->char('msk_color', 255)->nullable()->default("#1D7D5D");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ms_sub_kriteria', function (Blueprint $table) {
            $table->dropColumn('msk_color');
        });
    }
}
