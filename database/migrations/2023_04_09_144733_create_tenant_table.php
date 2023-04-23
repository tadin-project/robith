<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant', function (Blueprint $table) {
            $table->id("tenant_id");
            $table->char("tenant_nama", 255);
            $table->text("tenant_desc")->nullable();
            $table->boolean("tenant_status")->nullable()->default(true);
            $table->unsignedBigInteger("user_id");
            $table->unsignedSmallInteger("mku_id");
            $table->timestamps();

            $table->foreign("user_id", "tenant_user_id_fk")->references("user_id")->on("ms_users")->onUpdate("cascade")->onDelete("cascade");
            $table->foreign("mku_id", "tenant_mku_id_fk")->references("mku_id")->on("ms_kategori_usaha")->onUpdate("cascade")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant');
    }
}
