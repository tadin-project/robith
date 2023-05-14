<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConvertionValueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('convertion_value', function (Blueprint $table) {
            $table->unsignedSmallInteger('cval_id', true);
            $table->char('cval_kode', 20);
            $table->char('cval_nama', 255);
            $table->mediumInteger('cval_nilai')->nullable()->default(0);
            $table->char('cval_status')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('convertion_value');
    }
}
