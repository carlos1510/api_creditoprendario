<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 100)->nullable();
            $table->double('monto')->nullable();
            $table->date('fecha')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign("empresa_id")
            ->references("id")
            ->on("empresas")
            ->onDelete('set null');

            $table->foreign("user_id")
            ->references("id")
            ->on("users")
            ->onDelete('set null');

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
        Schema::dropIfExists('gastos');
    }
}
