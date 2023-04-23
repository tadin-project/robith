<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsesmenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asesmen', function (Blueprint $table) {
            $table->id("as_id");
            $table->unsignedBigInteger("tenant_id");
            $table->unsignedBigInteger("user_id")->comment("ms_users");
            $table->smallInteger("as_status")->comment("0=Belum selesai diisi;\n1=Sudah selesai diisi;\n2=Sudah selesai divalidasi;\n3=Ditolak;")->nullable()->default(0);
            $table->unsignedBigInteger("valid_by")->comment("ms_users")->nullable();
            $table->timestamps();

            $table->foreign("tenant_id", "asesmen_tenant_id_fk")->references("tenant_id")->on("tenant")->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asesmen');
    }
}
