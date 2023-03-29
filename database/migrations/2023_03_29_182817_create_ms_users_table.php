<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_users', function (Blueprint $table) {
            $table->id('user_id');
            $table->char('user_name', 255)->unique();
            $table->char('user_email', 255)->unique();
            $table->text('user_password');
            $table->char('user_fullname', 255)->nullable();
            $table->boolean('user_status')->nullable()->default(true);
            $table->unsignedSmallInteger('group_id')->nullable();
            $table->timestamps();

            $table->foreign('group_id', 'fk_ms_users_group_id')->references('group_id')->on('ms_groups')->onUpdate('cascade')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_users');
    }
}
