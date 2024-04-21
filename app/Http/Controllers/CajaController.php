<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index(Request $request) {
        $cajas = Caja::all()->where('estado', 1);

        return response()->json($cajas, 200);
    }

    public function show($id, Request $request){
        $caja = Caja::find($id);

        return response()->json($caja, 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fechaapertura' => 'required',
            'horaapertura' => 'required',
            'montoinicial' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $caja = new Caja();
        $caja->fechaapertura = $request->fechaapertura;
        $caja->horaapertura = $request->horaapertura;
        $caja->montoinicial = $request->montoinicial;
        $caja->fechacierre = null;
        $caja->horacierre = null;
        $caja->montocierre = null;
        $caja->montocredito = null;
        $caja->montogasto = null;
        $caja->montocobro = null;
        $caja->estado = 1;
        $caja->user_id = $request->user_id;
        $caja->empresa_id = $request->empresa_id;

        $caja->save();

        return response()->json($caja, 200);
    }

    public function update($id, Request $request) {
        $caja = Caja::find($id);

        $validator = Validator::make($request->all(), [
            'fechaapertura' => 'required',
            'horaapertura' => 'required',
            'montoinicial' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $caja->fechaapertura = $request->fechaapertura;
        $caja->horaapertura = $request->horaapertura;
        $caja->montoinicial = $request->montoinicial;
        $caja->fechacierre = isset($request->fechacierre)?$request->fechacierre:null;
        $caja->horacierre = isset($request->horacierre)?$request->horacierre:null;
        $caja->montocierre = isset($request->montocierre)?$request->montocierre:null;
        $caja->montocredito = isset($request->montocredito)?$request->montocredito:null;
        $caja->montogasto = isset($request->montogasto)?$request->montogasto:null;
        $caja->montocobro = isset($request->montocobro)?$request->montocobro:null;

        $caja->update();

        return response()->json($caja, 200);
    }

    public function cerrarCaja($id, Request $request) {
        $caja = Caja::find($id);
        $validator = Validator::make($request->all(), [
            'fechacierre' => 'required',
            'horacierre' => 'required',
            'montocierre' => 'required',
            'montocredito' => 'required',
            'montogasto' => 'required',
            'montocobro' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $caja->fechacierre = $request->fechacierre;
        $caja->horacierre = $request->horacierre;
        $caja->montocierre = $request->montocierre;
        $caja->montocredito = $request->montocredito;
        $caja->montogasto = $request->montogasto;
        $caja->montocobro = $request->montocobro;

        $caja->estado = 2; //caja cerrado

        $caja->update();

        return response()->json($caja, 200);
    }

    public function delete($id){
        $caja = Caja::find($id);
        $caja->estado = 0; //estado eliminado
        
        $caja->update();

        return response()->json(null, 204);
    }
}
