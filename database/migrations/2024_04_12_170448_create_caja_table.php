<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->id();
            $table->date('fechaapertura');
            $table->string('horaapertura', 10);
            $table->double('montoinicial');
            $table->date('fechacierre')->nullable();
            $table->string('horacierre', 10)->nullable();
            $table->double('montocobro')->nullable();
            $table->double('montocredito')->nullable();
            $table->double('montogasto')->nullable();
            $table->double('montocierre')->nullable();
            $table->integer('estado');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
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
        Schema::dropIfExists('caja');
    }
}
