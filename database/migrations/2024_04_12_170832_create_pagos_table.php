<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->integer('seriecorrelativo')->nullable();
            $table->integer('numerocorrelativo')->nullable();
            $table->string('codigogenerado'. 25)->nullable();
            $table->integer('numeropago')->nullable();
            $table->string('codigopago', 25)->nullable();
            $table->date('fecha');
            $table->date('fechavencimientoanterior')->nullable();
            $table->string('codigocredito', 25)->nullable();
            $table->string('codigocontrato', 25)->nullable();
            $table->double('capital');
            $table->double('interes');
            $table->double('interes_socio')->nullable();
            $table->double('igv')->nullable();
            $table->double('totalinteressocio')->nullable();
            $table->double('interes_negocio')->nullable();
            $table->double('total');
            $table->double('monto');
            $table->double('montorestante')->nullable();
            $table->integer('nro_dias')->nullable();
            $table->string('tiposervicio', 45)->nullable();
            $table->double('nuevocapital')->nullable();
            $table->string('plazo', 15)->nullable();
            $table->date('fechavencimientonuevo')->nullable();
            
            $table->boolean('estado');

            $table->unsignedBigInteger('tipo_comprobante_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->unsignedBigInteger('credito_id')->nullable();

            $table->foreign("tipo_comprobante_id")
            ->references("id")
            ->on("tipo_comprobantes")
            ->onDelete('set null');

            $table->foreign("user_id")
            ->references("id")
            ->on("users")
            ->onDelete('set null');

            $table->foreign("empresa_id")
            ->references("id")
            ->on("empresas")
            ->onDelete('set null');

            $table->foreign("credito_id")
            ->references("id")
            ->on("creditos")
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
        Schema::dropIfExists('pagos');
    }
}
