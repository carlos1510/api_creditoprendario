<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaldoalquilerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saldoalquiler', function (Blueprint $table) {
            $table->id();
            $table->integer('pagoalquilerid');
            $table->integer('empresasid');
            $table->date('fechainicio');
            $table->date('fechafinal');
            $table->integer('saldo');
            $table->integer('estadoactivacion')->nullable();
            $table->integer('estadopago')->nullable();
            $table->integer('estadomora')->nullable();
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
        Schema::dropIfExists('saldoalquiler');
    }
}
