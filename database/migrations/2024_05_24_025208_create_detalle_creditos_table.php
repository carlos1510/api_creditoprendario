<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleCreditosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_creditos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->string('valor1', 50)->nullable();
            $table->string('valor2', 50)->nullable();
            $table->string('valor3', 50)->nullable();
            $table->string('observaciones')->nullable();
            $table->double('valorizacion')->nullable();
            $table->integer('estado');
            $table->unsignedBigInteger('credito_id')->nullable();
            $table->unsignedBigInteger('servicio_id')->nullable();

            $table->foreign("credito_id")
                ->references("id")
                ->on("creditos")
                ->onDelete('set null');

            $table->foreign("servicio_id")
            ->references("id")
            ->on("servicios")
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
        Schema::dropIfExists('detalle_creditos');
    }
}
