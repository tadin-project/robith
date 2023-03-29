<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_groups', function (Blueprint $table) {
            $table->unsignedSmallInteger('group_id', true);
            $table->char('group_kode', 20)->unique();
            $table->char('group_nama', 255);
            $table->boolean('group_status')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_groups');
    }
}
