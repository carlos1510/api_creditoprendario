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
        Schema::create('pago_alquiler', function (Blueprint $table) {
            $table->id();
            $table->string('numerooperacion', 15)->nullable();
            $table->date('fecha');
            $table->double('monto');
            $table->string('descripcion')->nullable();
            $table->string('rutaimagen')->nullable();
            $table->boolean('estado');

            $table->unsignedBigInteger('tipo_banco_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign("tipo_banco_id")
                ->references("id")
                ->on("tipo_bancos")
                ->onDelete('set null');

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
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
        Schema::dropIfExists('pago_alquiler');
    }
}
