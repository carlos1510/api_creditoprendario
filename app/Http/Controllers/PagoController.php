<?php

namespace App\Http\Controllers;

use App\Models\Credito;
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
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $pago = new Pago();
        $pago->fecha = $request->fecha;
        $pago->capital = $request->capital;
        $pago->interes = $request->interes;
        $pago->total = $request->total;
        $pago->monto = $request->monto;
        $pago->montorestante = isset($request->montorestante)?$request->montorestante:null;
        $pago->descripcion = isset($request->descripcion)?$request->descripcion:null;
        $pago->estado = 1;
        //$pago->user_id = $request->user()->id;
        $pago->user_id = $request->user_id;
        $pago->empresa_id = $request->empresa_id;
        $pago->credito_id = $request->credito_id;
        $pago->save();

        if($pago->total == $pago->monto){
            //se termino el pago
            $credito = Credito::find($pago->credito_id);
            $credito->estado = 2;
            $credito->estados = 'PAGADO';
            $credito->update();
        }

        return response()->json(
            [
                'data' =>  $pago,
                'status' => 201,
                'ok' => true
            ]
        );
    }

    public function update($id, Request $request) {
        $pago = Pago::find($id);

        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'capital' => 'required',
            'monto' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $pago->fecha = $request->fecha;
        $pago->capital = $request->capital;
        $pago->interes = $request->interes;
        $pago->total = $request->total;
        $pago->monto = $request->monto;
        $pago->montorestante = isset($request->montorestante)?$request->montorestante:null;
        $pago->descripcion = isset($request->descripcion)?$request->descripcion:null;

        $pago->update();

        return response()->json(
            [
                'data' =>  $pago,
                'status' => 201,
                'ok' => true
            ]
        );
    }

    public function destroy($id) {
        $pago = Pago::find($id);

        $pago->estado = 1;

        return response()->json(
            [
                'data' =>  $pago,
                'status' => 201,
                'ok' => true
            ]
        );
    }
}
