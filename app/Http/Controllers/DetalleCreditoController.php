<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetalleCredito;

date_default_timezone_set('America/Lima');

class DetalleCreditoController extends Controller
{
    /*public function show($id) {
        $detalles = 
    }*/

    public function obtenerDetalleByIdCredito($idcredito, Request $request) {
        $detalles = DetalleCredito::where('estado',1)
        ->where('credito_id', $idcredito)->get();

        return response()->json(
            [
                'data' =>  $detalles,
                'status' => 200,
                'ok' => true
            ]
        );
    }
}
