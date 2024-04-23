<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Credito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreditoController extends Controller
{
    public function index(Request $request) {
        $creditos = Credito::all()->where('estado', 1);

        return response()->json($creditos, 200);
    }

    public function show($id, Request $request) {
        $credito = Credito::find($id);

        return response()->json($credito, 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'fechalimite' => 'required',
            'seriecorrelativo' => 'required',
            'numerocorrelativo' => 'required',
            'codigogenerado' => 'required',
            'tipomoneda' => 'required',
            'descripcion_bien' => 'required',
            'monto' => 'required',
            'interes' => 'required',
            'subtotal' => 'required',
            'total' => 'required',
            'total_texto' => 'required',
            'tipodocumento' => 'required',
            'numerodocumento' => 'required',
            'nombrescliente' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if(!$request->has('cliente_id')){
            $cliente = new Cliente();
            $cliente->tipodocumento = $request->tipodocumento;
            $cliente->numerodocumento = $request->numerodocumento;
            $cliente->nombrescliente = $request->nombrescliente;
            $cliente->direccion = $request->direccion;
            $cliente->referencia = $request->referencia;
            $cliente->telefono1 = $request->telefono1;
            $cliente->telefono2 = $request->telefono2;
            $cliente->email = $request->email;
            $cliente->latitud = $request->latitud;
            $cliente->longitud = $request->longitud;
            $cliente->estado = 1;
            $cliente->save();

            $request->merge(['cliente_id' => $cliente->id]);
        }else {
            $cliente = Cliente::find($request->cliente_id);
            $cliente->tipodocumento = $request->tipodocumento;
            $cliente->numerodocumento = $request->numerodocumento;
            $cliente->nombrescliente = $request->nombrescliente;
            $cliente->direccion = $request->direccion;
            $cliente->referencia = $request->referencia;
            $cliente->telefono1 = $request->telefono1;
            $cliente->telefono2 = $request->telefono2;
            $cliente->email = $request->email;
            $cliente->latitud = $request->latitud;
            $cliente->longitud = $request->longitud;
            $cliente->update();
        }

        $credito = new Credito();
        $credito->fecha = $request->fecha;
        $credito->fechalimite = $request->fechalimite;
        $credito->seriecorrelativo = $request->seriecorrelativo;
        $credito->numerocorrelativo = $request->numerocorrelativo;
        $credito->codigogenerado = $request->codigogenerado;
        $credito->tipomoneda = $request->tipomoneda;
        $credito->descripcion_bien = $request->descripcion_bien;
        $credito->igv = $request->igv;
        $credito->monto = $request->monto;
        $credito->interes = $request->interes;
        $credito->subtotal = $request->subtotal;
        $credito->total = $request->total;
        $credito->total_texto = $request->total_texto;
        $credito->descuento = $request->descuento;
        $credito->estados = 'ACTIVO';
        $credito->estado = 1;
        $credito->user_id = $request->user_id;
        $credito->tipo_comprobante_id = $request->tipo_comprobante_id;
        $credito->cliente_id = $request->cliente_id;
        $credito->servicio_id = $request->servicio_id;
        $credito->empresa_id = $request->empresa_id;
        $credito->save();

        return response()->json($credito, 201);
    }

    public function update($id, Request $request){
        $credito = Credito::find($id);

        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'fechalimite' => 'required',
            'seriecorrelativo' => 'required',
            'numerocorrelativo' => 'required',
            'codigogenerado' => 'required',
            'tipomoneda' => 'required',
            'descripcion_bien' => 'required',
            'monto' => 'required',
            'interes' => 'required',
            'subtotal' => 'required',
            'total' => 'required',
            'total_texto' => 'required',
            'tipodocumento' => 'required',
            'numerodocumento' => 'required',
            'nombrescliente' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if(!$request->has('cliente_id')){
            $cliente = new Cliente();
            $cliente->tipodocumento = $request->tipodocumento;
            $cliente->numerodocumento = $request->numerodocumento;
            $cliente->nombrescliente = $request->nombrescliente;
            $cliente->direccion = $request->direccion;
            $cliente->referencia = $request->referencia;
            $cliente->telefono1 = $request->telefono1;
            $cliente->telefono2 = $request->telefono2;
            $cliente->email = $request->email;
            $cliente->latitud = $request->latitud;
            $cliente->longitud = $request->longitud;
            $cliente->estado = 1;
            $cliente->save();

            $request->merge(['cliente_id' => $cliente->id]);
        }else {
            $cliente = Cliente::find($request->cliente_id);
            $cliente->tipodocumento = $request->tipodocumento;
            $cliente->numerodocumento = $request->numerodocumento;
            $cliente->nombrescliente = $request->nombrescliente;
            $cliente->direccion = $request->direccion;
            $cliente->referencia = $request->referencia;
            $cliente->telefono1 = $request->telefono1;
            $cliente->telefono2 = $request->telefono2;
            $cliente->email = $request->email;
            $cliente->latitud = $request->latitud;
            $cliente->longitud = $request->longitud;
            $cliente->update();
        }

        $credito->fecha = $request->fecha;
        $credito->fechalimite = $request->fechalimite;
        $credito->seriecorrelativo = $request->seriecorrelativo;
        $credito->numerocorrelativo = $request->numerocorrelativo;
        $credito->codigogenerado = $request->codigogenerado;
        $credito->tipomoneda = $request->tipomoneda;
        $credito->descripcion_bien = $request->descripcion_bien;
        $credito->igv = $request->igv;
        $credito->monto = $request->monto;
        $credito->interes = $request->interes;
        $credito->subtotal = $request->subtotal;
        $credito->total = $request->total;
        $credito->total_texto = $request->total_texto;
        $credito->descuento = $request->descuento;
        $credito->tipo_comprobante_id = $request->tipo_comprobante_id;
        $credito->cliente_id = $request->cliente_id;
        $credito->servicio_id = $request->servicio_id;

        $credito->update();

        return response()->json($credito, 201);
    }

    public function destroy($id) {
        $credito = Credito::find($id);

        $credito->estados = 'INACTIVO';
        $credito->estado = 0;

        $credito->update();

        return response()->json(null, 201);
    }
}
