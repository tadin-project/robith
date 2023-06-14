<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMkColorToMsKriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ms_kriteria', function (Blueprint $table) {
            $table->char('mk_color', 255)->nullable()->default("#322BBA");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ms_kriteria', function (Blueprint $table) {
            $table->dropColumn('mk_color');
        });
    }
}
