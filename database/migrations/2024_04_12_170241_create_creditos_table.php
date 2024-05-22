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
            $table->integer('seriecorrelativo')->nullable();
            $table->integer('numerocorrelativo')->nullable();
            $table->string('codigogenerado',25)->nullable();
            $table->integer('numerocredito')->nullable();
            $table->string('codigocredito', 25)->nullable();
            $table->integer('numerocontrato')->nullable();
            $table->string('codigocontrato', 25)->nullable();
            $table->string('tipomoneda', 25);
            $table->string('descripcion_bien');
            $table->double('igv')->nullable();
            $table->double('monto');
            $table->double('interes')->nullable();
            $table->double('subtotal')->nullable();
            $table->double('total')->nullable();
            $table->string('total_texto');
            $table->double('descuento')->nullable();
            $table->double('montoactual')->nullable();
            $table->string('estados', 25);
            $table->boolean('estado');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('tipo_comprobante_id')->nullable();
            $table->unsignedBigInteger('servicio_id')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();

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
        Schema::dropIfExists('creditos');
    }
}
