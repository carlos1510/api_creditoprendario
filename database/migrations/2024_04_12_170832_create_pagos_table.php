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
            
            $table->date('fecha');
            $table->double('capital');
            $table->double('interes');
            $table->double('total');
            $table->double('monto');
            $table->double('montorestante')->nullable();
            $table->string('descripcion')->nullable();
            
            $table->boolean('estado');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->unsignedBigInteger('credito_id')->nullable();

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
