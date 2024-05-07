<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('tipodocumentoid');
            $table->string('numerodocumento', 15);
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('acceso');
            $table->string('rol')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();

            $table->foreign("empresa_id")
                ->references("id")
                ->on("empresas")
                ->onDelete('set null');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
