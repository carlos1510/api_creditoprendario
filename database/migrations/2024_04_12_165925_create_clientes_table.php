<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->integer("tipodocumento");
            $table->string("numerodocumento", 15);
            $table->string("nombrescliente", 150);
            $table->string("direccion")->nullable();
            $table->string("referencia")->nullable();
            $table->string("telefono1", 25)->nullable();
            $table->string("telefono2", 25)->nullable();
            $table->string("email", 100)->nullable();
            $table->double("latitud")->nullable();
            $table->double("longitud")->nullable();
            $table->boolean("estado");
            
            $table->unsignedBigInteger("empresa_id")->nullable();
            $table->unsignedBigInteger("user_id")->nullable();

            $table->foreign("empresa_id")
                ->references("id")
                ->on("empresas")
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
        Schema::dropIfExists('clientes');
    }
}
