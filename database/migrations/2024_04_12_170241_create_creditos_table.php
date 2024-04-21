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
            $table->date('fecha');
            $table->date('fechalimite');
            $table->integer('seriecorrelativo');
            $table->integer('numerocorrelativo');
            $table->string('codigogenerado');
            $table->string('tipomoneda');
            $table->string('descripcion_bien');
            $table->double('igv')->nullable();
            $table->double('monto');
            $table->double('interes');
            $table->double('subtotal');
            $table->double('total');
            $table->string('total_texto');
            $table->double('descuento')->nullable();
            $table->double('montoactual')->nullable();
            $table->string('estados');
            $table->boolean('estado');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('tipo_comprobante_id')->nullable();
            $table->unsignedBigInteger('servicio_id')->nullable();

            $table->foreign("user_id")
            ->references("id")
            ->on("users")
            ->onDelete('set null');

            $table->foreign("cliente_id")
            ->references("id")
            ->on("clientes")
            ->onDelete('set null');

            $table->foreign("tipo_comprobante_id")
            ->references("id")
            ->on("tipo_comprobantes")
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
        Schema::dropIfExists('creditos');
    }
}
