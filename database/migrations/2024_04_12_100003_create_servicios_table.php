<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string("tiposervicio", 45);
            $table->string("descripcion")->nullable();
            $table->string("periodo", 25);
            $table->integer("numeroperiodo");
            $table->double("porcentajesocio");
            $table->double("porcentajenegocio");
            $table->double("porcentaje");
            $table->boolean("estado");
            $table->unsignedBigInteger("empresa_id")->nullable();

            $table->foreign("empresa_id")
            ->references("id")
            ->on("empresas")
            ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicios');
    }
}
