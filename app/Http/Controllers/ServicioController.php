<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
date_default_timezone_set('America/Lima');

class ServicioController extends Controller
{
    public function index(Request $request){
       if(auth()->user()->rol != 'Administrador'){
        $servicios = Servicio::where('estado', 1)
        ->where('empresa_id',auth()->user()->empresa_id)->get();
       }else{
        $servicios = Servicio::where('estado', 1)->get();
       }

        return response()->json(
            [
                'data' => $servicios,
                'ok' => true,
                'status' => 200,
                'message' => 'Servicios obtenidos correctamente'
            ]
        );
    }

    public function show($id, Request $request){
        $servicio = Servicio::find($id);

        return response()->json(
            [
                'data' => $servicio,
                'status' => 200,
                'ok' => true,
                'message' => 'Servicios obtenidos correctamente'
            ]
        );
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
        $servicio->porcentajesocio = $request->porcentajesocio;
        $servicio->porcentajenegocio = $request->porcentajenegocio;
        $servicio->porcentaje = $request->porcentaje;
        $servicio->estado = 1;
        $servicio->empresa_id = auth()->user()->empresa_id;

        $servicio->save();

        return response()->json(
            [
                'data' => $servicio,
                'status' => 201,
                'ok' => true,
                'message' => 'Servicios obtenidos correctamente'
            ],201
        );
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
        $servicio->porcentajesocio = $request->porcentajesocio;
        $servicio->porcentajenegocio = $request->porcentajenegocio;
        $servicio->porcentaje = $request->porcentaje;

        $servicio->update();

        return response()->json(
            [
                'data' => $servicio,
                'status' => 201,
                'ok' => true,
                'message' => 'Servicios obtenidos correctamente'
            ],201
        );
    }

    public function destroy($id) {
        $servicio = Servicio::find($id);
        $servicio->estado = 0;

        $servicio->update();

        return response()->json(
            [
                'data' => $servicio,
                'status' => 201,
                'ok' => true,
                'message' => 'Servicios obtenidos correctamente'
            ],201
        );
    }
}
