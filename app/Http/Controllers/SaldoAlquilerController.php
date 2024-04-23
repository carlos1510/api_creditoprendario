<?php

namespace App\Http\Controllers;

use App\Models\SaldoAlquiler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaldoAlquilerController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required',
            'fecha_final' => 'required',
            'saldo' => 'required',
            'estadoactivacion' => 'required',
            'estadopago' => 'required',
            'estadopago' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        
        $saldoAlquiler = new SaldoAlquiler();
        $saldoAlquiler->fecha_inicio = $request->fecha_inicio;
        $saldoAlquiler->fecha_final = $request->fecha_final;
        $saldoAlquiler->saldo = $request->saldo;
        $saldoAlquiler->estadoactivacion = $request->estadoactivacion;
        $saldoAlquiler->estadopago = $request->estadopago;
        $saldoAlquiler->pago_alquiler_id = $request->pago_alquiler_id;
        $saldoAlquiler->save();

        return response()->json([
            'message' => 'Saldo Alquiler creado', 'status' => 201]
        );
    }
}
