<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->integer('empresasid');
            $table->integer('clientesid');
            $table->integer('tipocomprobantesid');
            $table->integer('serviciosid');
            $table->date('fecha');
            $table->date('fechalimite');
            $table->integer('seriecorrelativo');
            $table->integer('numerocorrelativo');
            $table->string('codigogenerado');
            $table->string('tipomoneda');
            $table->double('igv')->nullable();
            $table->double('monto');
            $table->double('interes');
            $table->double('subtotal');
            $table->double('total');
            $table->string('total_texto');
            $table->double('descuento');
            $table->double('montoactual');
            $table->integer('usersid');
            $table->integer('estado');
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
        Schema::dropIfExists('creditos');
    }
}
