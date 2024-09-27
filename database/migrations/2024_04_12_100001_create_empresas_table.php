<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombrenegocio', 100);
            $table->integer('tipodocumentoid');
            $table->string('razonsocial', 100)->nullable();
            $table->string('nombre', 100);
            $table->string('numerodocumento', 15);
            $table->string('email', 100)->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 15)->nullable();
            $table->string('rutaimagen')->nullable();
            $table->integer('gps')->nullable();
            $table->string('tipomoneda', 15)->nullable();
            $table->string('simbolomoneda', 5)->nullable();
            $table->string('rucsocio', 15)->nullable();
            $table->string('razonsocialsocio', 100)->nullable();
            $table->string('razonsocial', 100)->nullable();

            $table->boolean('estado');

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
        Schema::dropIfExists('empresas');
    }
}
