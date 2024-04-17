<?php

namespace Database\Seeders;

use App\Models\TipoBanco;
use Illuminate\Database\Seeder;

class TipoBancoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipoBanco1 = new TipoBanco();

        $tipoBanco1->nombre = "BANCO DE LA NACION";
        $tipoBanco1->nombreabreviado = "BN";
        $tipoBanco1->estado = true;

        $tipoBanco1->save();

        $tipoBanco2 = new TipoBanco();

        $tipoBanco2->nombre = "BANCO DE CREDITO DEL PERU";
        $tipoBanco2->nombreabreviado = "BCP";
        $tipoBanco2->estado = true;

        $tipoBanco2->save();

        $tipoBanco3 = new TipoBanco();

        $tipoBanco3->nombre = "YAPE";
        $tipoBanco3->nombreabreviado = "YAPE";
        $tipoBanco3->estado = true;

        $tipoBanco3->save();

        $tipoBanco4 = new TipoBanco();

        $tipoBanco4->nombre = "EFECTIVO";
        $tipoBanco4->nombreabreviado = "EFECTIVO";
        $tipoBanco4->estado = true;

        $tipoBanco4->save();
        
    }
}
