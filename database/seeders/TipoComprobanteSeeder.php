<?php

namespace Database\Seeders;

use App\Models\TipoComprobante;
use Illuminate\Database\Seeder;

class TipoComprobanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tipoComprobante1 = new TipoComprobante();
        $tipoComprobante1->nombre = 'FACTURA';
        $tipoComprobante1->nombreabreviado = 'FACTURA';
        $tipoComprobante1->anotacion = 'FF';

        $tipoComprobante1->save();

        $tipoComprobante2 = new TipoComprobante();
        $tipoComprobante2->nombre = 'BOLETA';
        $tipoComprobante2->nombreabreviado = 'BOLETA';
        $tipoComprobante2->anotacion = 'BB';

        $tipoComprobante2->save();

        $tipoComprobante3 = new TipoComprobante();
        $tipoComprobante3->nombre = 'NOTA DE VENTA';
        $tipoComprobante3->nombreabreviado = 'NDV';
        $tipoComprobante2->anotacion = 'NV';

        $tipoComprobante3->save();
    }
}
