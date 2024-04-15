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
            $table->string('nombre', 100);
            $table->string('numerodocumento', 15);
            $table->string('email', 100);
            $table->string('direccion');
            $table->string('telefono', 15);
            $table->string('rutaimagen');
            $table->integer('gps');
            $table->string('tipomoneda', 15);
            $table->string('simbolomoneda', 5);
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