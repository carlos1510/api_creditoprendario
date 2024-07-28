<?php

namespace App\Http\Controllers;

use App\Http\Utils\Util;
use App\Models\PagoAlquiler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
date_default_timezone_set('America/Lima');

class PagoAlquilerController extends Controller
{
    public function index(Request $request) {
        if(auth()->user()->rol != 'Administrador'){
            $pagoAlquileres = PagoAlquiler::where('estado', 1)
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        }else{
            $pagoAlquileres = PagoAlquiler::where('estado', 1)->get();
        }

        return response()->json([
            'data' => $pagoAlquileres, 
            'status' => 200,
            'ok' => true
        ],200);
    }

    public function indexFiltro($fecha_ini, $fecha_fin, Request $request) {
        $inicio = $fecha_ini!="null"?$fecha_ini:date("Y-m-01");
        $fin = $fecha_fin!="null"?$fecha_fin:date("Y-m-t");
        if(auth()->user()->rol !="Administrador"){
            $pagoAlquileres = PagoAlquiler::select('pago_alquiler.id','pago_alquiler.tipo_banco_id','pago_alquiler.fecha','pago_alquiler.monto','pago_alquiler.descripcion',
            'tipo_bancos.nombre as nom_tipoBanco','b.estadoactivacion','b.estadopago','b.id as saldo_alquiler_id')
            ->selectRaw("IF(DATEDIFF(b.fecha_final,CURDATE())<=0,0,DATEDIFF(b.fecha_final,CURDATE())) AS saldo")
            ->join('tipo_bancos','pago_alquiler.tipo_banco_id', '=','tipo_bancos.id')
            ->leftjoin('saldo_alquiler as b','pago_alquiler.id','=','b.pago_alquiler_id')
            ->where('pago_alquiler.estado', 1)
            ->where('pago_alquiler.user_id', auth()->user()->id)
            ->whereBetween('pago_alquiler.fecha', [$inicio, $fin])
            ->orderBy('pago_alquiler.created_at', 'desc')
            ->get();
        }else{
            $pagoAlquileres = PagoAlquiler::select('pago_alquiler.id','pago_alquiler.tipo_banco_id','pago_alquiler.fecha','pago_alquiler.monto','pago_alquiler.descripcion',
            'tipo_bancos.nombre as nom_tipoBanco','b.estadoactivacion','b.estadopago','b.id as saldo_alquiler_id')
            ->selectRaw("IF(DATEDIFF(b.fecha_final,CURDATE())<=0,0,DATEDIFF(b.fecha_final,CURDATE())) AS saldo")
            ->join('tipo_bancos','pago_alquiler.tipo_banco_id', '=','tipo_bancos.id')
            ->leftjoin('saldo_alquiler as b','pago_alquiler.id','=','b.pago_alquiler_id')
            ->where('pago_alquiler.estado', 1)
            ->whereBetween('pago_alquiler.fecha', [$inicio, $fin])
            ->orderBy('pago_alquiler.created_at', 'desc')
            ->get();
        }

        return response()->json([
            'data' => $pagoAlquileres, 
            'status' => 200,
            'ok' => true
        ],200);
    }

    public function show($id, Request $request) {
        $pagoAlquiler = PagoAlquiler::find($id);

        return response()->json([
            'data' => $pagoAlquiler, 
            'status' => 200,
            'ok' => true
        ],200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'monto' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $pagoAlquiler = new PagoAlquiler();
        $pagoAlquiler->numerooperacion = $request->numerooperacion?$request->numerooperacion:null;
        $pagoAlquiler->fecha = $request->fecha;
        $pagoAlquiler->monto = $request->monto;
        $pagoAlquiler->descripcion = $request->descripcion?$request->descripcion:null;
        $pagoAlquiler->rutaimagen = null;
        $pagoAlquiler->estado = 1;
        $pagoAlquiler->tipo_banco_id = $request->tipo_banco_id;
        $pagoAlquiler->user_id = isset($request->user_id)?$request->user_id:auth()->user()->id;
        $pagoAlquiler->empresa_id = auth()->user()->empresa_id;
        $pagoAlquiler->save();

        return response()->json([
            'data' => $pagoAlquiler, 
            'status' => 201,
            'ok' => true
        ],201);

    }

    public function update($id, Request $request) {
        $pagoAlquiler = PagoAlquiler::find($id);

        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'monto' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $pagoAlquiler->numerooperacion = $request->numerooperacion?$request->numerooperacion:null;
        $pagoAlquiler->fecha = $request->fecha;
        $pagoAlquiler->monto = $request->monto;
        $pagoAlquiler->descripcion = $request->descripcion?$request->descripcion:null;

        $pagoAlquiler->tipo_banco_id = $request->tipo_banco_id;

        $pagoAlquiler->update();

        return response()->json([
            'data' => $pagoAlquiler, 
            'status' => 201,
            'ok' => true
        ],201);
    }

    public function destroy($id) {
        $pagoAlquiler = PagoAlquiler::find($id);

        $pagoAlquiler->estado = 0;

        $pagoAlquiler->update();

        return response()->json([
            'data' => $pagoAlquiler, 
            'status' => 201,
            'ok' => true
        ]);
    }
}
