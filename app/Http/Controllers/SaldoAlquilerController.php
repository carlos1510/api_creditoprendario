<?php

namespace App\Http\Controllers;

use App\Models\SaldoAlquiler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
date_default_timezone_set('America/Lima');

class SaldoAlquilerController extends Controller
{
    public function show(Request $request) {
        $saldoAlquiler = DB::selectOne("SELECT IF(DATEDIFF(MAX(sa.fecha_final),CURDATE())<=0,0,DATEDIFF(MAX(sa.fecha_final),CURDATE())) AS saldo, DATE_FORMAT(MAX(sa.fecha_final),'%d/%m/%Y') AS fecha, sa.estadopago
                    FROM saldo_alquiler sa INNER JOIN pago_alquiler pa ON sa.pago_alquiler_id=pa.id
                     WHERE pa.empresa_id=? GROUP BY sa.estadopago LIMIT 1", [auth()->user()->empresa_id]);

        return response()->json([
            'data' => $saldoAlquiler, 
            'status' => 200,
            'ok' => true
        ],200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fecha_inicio' => 'required',
            'fecha_final' => 'required',
            'estadoactivacion' => 'required',
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
