<?php

namespace App\Http\Controllers;

use App\Models\PagoAlquiler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagoAlquilerController extends Controller
{
    public function index(Request $request) {
        $pagoAlquileres = PagoAlquiler::all()->where('estado', 1);

        return response()->json($pagoAlquileres);
    }

    public function show($id, Request $request) {
        $pagoAlquiler = PagoAlquiler::find($id);

        return response()->json($pagoAlquiler, 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'numerooperacion' => 'required',
            'fecha' => 'required',
            'monto' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $pagoAlquiler = new PagoAlquiler();
        $pagoAlquiler->numerooperacion = $request->numerooperacion;
        $pagoAlquiler->fecha = $request->fecha;
        $pagoAlquiler->monto = $request->monto;
        $pagoAlquiler->descripcion = $request->descripcion;
        $pagoAlquiler->rutaimagen = null;
        $pagoAlquiler->estado = 1;
        $pagoAlquiler->tipo_banco_id = $request->tipo_banco_id;
        $pagoAlquiler->user_id = $request->user_id;
        $pagoAlquiler->save();

        return response()->json($pagoAlquiler, 201);

    }

    public function update($id, Request $request) {
        $pagoAlquiler = PagoAlquiler::find($id);
        $validator = Validator::make($request->all(), [
            'numerooperacion' => 'required',
            'fecha' => 'required',
            'monto' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $pagoAlquiler->numerooperacion = $request->numerooperacion;
        $pagoAlquiler->fecha = $request->fecha;
        $pagoAlquiler->monto = $request->monto;
        $pagoAlquiler->descripcion = $request->descripcion;
        $pagoAlquiler->rutaimagen = null;

        $pagoAlquiler->tipo_banco_id = $request->tipo_banco_id;

        $pagoAlquiler->update();

        return response()->json($pagoAlquiler, 204);
    }

    public function destroy($id) {
        $pagoAlquiler = PagoAlquiler::find($id);

        $pagoAlquiler->estado = 1;

        $pagoAlquiler->update();

        return response()->json(null, 204);
    }
}
