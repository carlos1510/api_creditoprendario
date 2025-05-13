<?php

namespace App\Http\Controllers;

date_default_timezone_set('America/Lima');

use App\Models\Gasto;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    public function index(Request $request) {
        if(auth()->user()->rol != "Administrador"){
            $gastos = Gasto::where('estado', 1)
            ->where('empresa_id', auth()->user()->empresa_id)->get();
        }else {
            $gastos = Gasto::where('estado', 1)->get();
        }

        return response()->json([
            'data' => $gastos, 
            'status' => 200,
            'ok' => true
        ],200);
    }

    public function indexFilter($fecha_ini, $fecha_fin, Request $request){
        $inicio = $fecha_ini!="null"?$fecha_ini:date("Y-m-01");
        $fin = $fecha_fin!="null"?$fecha_fin:date("Y-m-t");

        if(auth()->user()->rol != "Administrador"){
            $gastos = Gasto::where('estado', 1)
            ->where('empresa_id', auth()->user()->empresa_id)
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderBy('created_at','desc')
            ->get();
        }else {
            $gastos = Gasto::where('estado', 1)
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderBy('created_at','desc')
            ->get();
        }

        return response()->json(
            [
                'data' => $gastos,
                'status' => 200,
                'ok' => true,
            ],200
        );
    }

    public function store(Request $request) {
        /*$validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:100',
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 422,
                'ok' => false
            ], 422);
        }*/

        $gasto = new Gasto();
        $gasto->descripcion = $request->descripcion;
        $gasto->monto = $request->monto;
        $gasto->fecha = $request->fecha;
        $gasto->empresa_id = auth()->user()->empresa_id;
        $gasto->user_id = auth()->user()->id;
        $gasto->estado = 1;

        if ($gasto->save()) {
            return response()->json([
                'data' => $gasto,
                'status' => 200,
                'ok' => true
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error al guardar el gasto',
                'status' => 500,
                'ok' => false
            ], 500);
        }
    }

    public function show($id) {
        $gasto = Gasto::find($id);

        if ($gasto) {
            return response()->json([
                'data' => $gasto,
                'status' => 200,
                'ok' => true
            ], 200);
        } else {
            return response()->json([
                'error' => 'Gasto no encontrado',
                'status' => 404,
                'ok' => false
            ], 404);
        }
    }

    public function update($id, Request $request) {
        $gasto = Gasto::find($id);

        if (!$gasto) {
            return response()->json([
                'error' => 'Gasto no encontrado',
                'status' => 404,
                'ok' => false
            ], 404);
        }

        $gasto->descripcion = $request->descripcion;
        $gasto->monto = $request->monto;
        $gasto->fecha = $request->fecha;
        $gasto->user_id = auth()->user()->id;

        if ($gasto->save()) {
            return response()->json([
                'data' => $gasto,
                'status' => 200,
                'ok' => true
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error al actualizar el gasto',
                'status' => 500,
                'ok' => false
            ], 500);
        }
    }

    public function destroy($id) {
        $gasto = Gasto::find($id);

        if (!$gasto) {
            return response()->json([
                'error' => 'Gasto no encontrado',
                'status' => 404,
                'ok' => false
            ], 404);
        }

        $gasto->estado = 0;

        if ($gasto->save()) {
            return response()->json([
                'data' => $gasto,
                'status' => 200,
                'ok' => true
            ], 200);
        } else {
            return response()->json([
                'error' => 'Error al eliminar el gasto',
                'status' => 500,
                'ok' => false
            ], 500);
        }
    }

}
