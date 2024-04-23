<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagoController extends Controller
{
    public function index(Request $request){
        $pagos = Pago::all()->where('estado', 1);

        return response()->json($pagos);
    }

    public function show($id, Request $request) {
        $pago = Pago::find($id);
        
        return response()->json($pago);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'capital' => 'required',
            'monto' => 'required',
            'montorestante' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $pago = new Pago();
        $pago->fecha = $request->fecha;
        $pago->capital = $request->capital;
        $pago->monto = $request->monto;
        $pago->montorestante = $request->montorestante;
        $pago->descripcion = $request->descripcion;
        $pago->estado = 1;
        //$pago->user_id = $request->user()->id;
        $pago->user_id = $request->user_id;
        $pago->empresa_id = $request->empresa_id;
        $pago->credito_id = $request->credito_id;
        $pago->save();

        return response()->json($pago, 201);
    }

    public function update($id, Request $request) {
        $pago = Pago::find($id);

        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'capital' => 'required',
            'monto' => 'required',
            'montorestante' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $pago->fecha = $request->fecha;
        $pago->capital = $request->capital;
        $pago->monto = $request->monto;
        $pago->montorestante = $request->montorestante;
        $pago->descripcion = $request->descripcion;

        $pago->update();

        return response()->json($pago, 201);
    }

    public function destroy($id) {
        $pago = Pago::find($id);

        $pago->estado = 1;

        return response()->json($pago, 201);
    }
}
