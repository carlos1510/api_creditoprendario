<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServicioController extends Controller
{
    public function index(Request $request){
        $servicios = Servicio::all()->where('estado', 1);

        return response()->json($servicios, 200);
    }

    public function show($id, Request $request){
        $servicio = Servicio::find($id);

        return response()->json($servicio, 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'tiposervicio' => 'required',
            'periodo' => 'required',
            'numeroperiodo' => 'required',
            'porcentaje' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $servicio = new Servicio();
        $servicio->tiposervicio = $request->tiposervicio;
        $servicio->descripcion = isset($request->descripcion)?$request->descripcion:null;
        $servicio->periodo = $request->periodo;
        $servicio->numeroperiodo = $request->numeroperiodo;
        $servicio->porcentaje = $request->porcentaje;
        $servicio->estado = 1;
        $servicio->empresa_id = $request->empresa_id;

        $servicio->save();

        return response()->json($servicio, 201);
    }

    public function update($id, Request $request){
        $servicio = Servicio::find($id);

        $validator = Validator::make($request->all(), [
            'tiposervicio' => 'required',
            'periodo' => 'required',
            'numeroperiodo' => 'required',
            'porcentaje' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $servicio->tiposervicio = $request->tiposervicio;
        $servicio->descripcion = isset($request->descripcion)?$request->descripcion:null;
        $servicio->periodo = $request->periodo;
        $servicio->numeroperiodo = $request->numeroperiodo;
        $servicio->porcentaje = $request->porcentaje;

        $servicio->update();

        return response()->json($servicio, 201);
    }

    public function delete($id) {
        $servicio = Servicio::find($id);
        $servicio->estado = 0;

        $servicio->update();

        return response()->json($servicio, 201);
    }
}
