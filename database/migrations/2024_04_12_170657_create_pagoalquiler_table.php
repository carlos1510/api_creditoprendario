<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagoalquilerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagoalquiler', function (Blueprint $table) {
            $table->id();
            $table->integer('tipobancosid');
            $table->string('numerooperacion', 15)->nullable();
            $table->date('fecha');
            $table->double('monto');
            $table->string('descripcion')->nullable();
            $table->string('rutaimagen')->nullable();
            $table->integer('empresasid');
            $table->integer('usersid');
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
        Schema::dropIfExists('pagoalquiler');
    }
}
