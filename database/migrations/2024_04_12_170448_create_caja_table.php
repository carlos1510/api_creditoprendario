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
            $table->integer('usersid');
            $table->integer('empresasid');
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
