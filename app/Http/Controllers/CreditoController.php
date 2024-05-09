<?php

namespace App\Http\Controllers;

use App\Http\Utils\Util;
use App\Models\Cliente;
use App\Models\Credito;
use App\Models\TipoComprobante;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreditoController extends Controller
{
    public function index($responsableId, $fecha_ini, $fecha_fin, $nro_documento,Request $request) {
        if($responsableId==0 && $fecha_ini=="null" && $fecha_fin=="null"){
            $inicio = $fecha_ini!="null"?$fecha_ini:date("Y-m-01");
            $fin = $fecha_fin!="null"?$fecha_fin:date("Y-m-t");
            $creditos = Credito::select('creditos.id', 'creditos.fecha', 'creditos.fechalimite', 'creditos.seriecorrelativo', 'creditos.numerocorrelativo', 'creditos.codigogenerado', 'creditos.tipomoneda', 'creditos.descripcion_bien'
            ,'creditos.igv', 'creditos.monto', 'creditos.interes', 'creditos.subtotal', 'creditos.total', 'creditos.total_texto', 'creditos.descuento', 'creditos.montoactual', 'creditos.estados', 'creditos.user_id', 'creditos.cliente_id',
            'creditos.tipo_comprobante_id', 'creditos.servicio_id', 'b.tipodocumento', 'b.numerodocumento', 'b.nombrescliente', 'b.direccion', 'b.referencia', 'b.telefono1', 'b.telefono2'
            , 'b.email')
            ->join('clientes as b','creditos.cliente_id', '=','b.id')
            ->where('creditos.estado', 1)
            ->whereBetween('creditos.fecha', [$inicio, $fin])
            ->get();
        }else{
            $sql = "SELECT a.id, a.fecha, a.fechalimite, a.seriecorrelativo, a.numerocorrelativo, a.codigogenerado, a.tipomoneda, a.descripcion_bien
                ,a.igv, a.monto, a.interes, a.subtotal, a.total, a.total_texto, a.descuento, a.montoactual, a.estados, a.user_id, a.cliente_id,
                a.tipo_comprobante_id, a.servicio_id, b.tipodocumento, b.numerodocumento, b.nombrescliente, b.direccion, b.referencia, b.telefono1, b.telefono2
                , b.email 
                FROM creditos a JOIN clientes b ON a.cliente_id=b.id 
                WHERE a.estado=1 
                ".(isset($fecha_ini)?($fecha_ini!="null"?(isset($fecha_fin)?($fecha_fin!="null"?" AND a.fecha BETWEEN '$fecha_ini' AND '$fecha_fin' ":""):""):""):"").
                (isset($responsableId)?($responsableId!=0?" AND a.user_id=$responsableId ":""):"").
                (isset($nro_documento)?($nro_documento!=0?" AND b.numerodocumento=''":""):"");
            $creditos = DB::select($sql);
        }

        return response()->json(
            [
                'data' =>  $creditos,
                'status' => 200,
                'ok' => true
            ]
        );
    }

    public function show($id, Request $request) {
        $credito = Credito::find($id);

        return response()->json($credito, 200);
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'fechalimite' => 'required',
            'codigogenerado' => 'required',
            'tipomoneda' => 'required',
            'descripcion_bien' => 'required',
            'monto' => 'required',
            'interes' => 'required',
            'total' => 'required',
            'total_texto' => 'required',
            'tipodocumento' => 'required',
            'numerodocumento' => 'required',
            'nombrescliente' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if(is_null($request->cliente_id)){
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
        $credito->fechalimite = Util::convertirStringFecha($request->fechalimite, false);
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

    public function getUltimoNroComprobante($tipoComprobanteID, Request $request){
        $nroComprobante = Credito::selectRaw("IF(ISNULL(MAX(numerocorrelativo)), 0, MAX(numerocorrelativo)) AS numero")
            ->where('tipo_comprobante_id', $tipoComprobanteID)
            ->first();

        $nroSerie = Credito::selectRaw("IF(ISNULL(MAX(seriecorrelativo)), 0,MAX(seriecorrelativo)) as serie")
        ->where('tipo_comprobante_id', $tipoComprobanteID)
        ->first();

        $tipoComprobante = TipoComprobante::find($tipoComprobanteID);

        $codigoGenerado = $tipoComprobante->anotacion.sprintf("%02d",(int)$nroSerie->serie + 1)."-".sprintf("%04d", ($nroComprobante->numero + 1));
        $seriecorrelativo = $nroSerie->serie + 1;
        $numerocorrelativo = $nroComprobante->numero + 1;

        return response()->json(
            [
                'data' => array('codigogenerado' => $codigoGenerado, 'seriecorrelativo' => $seriecorrelativo, 'numerocorrelativo' => $numerocorrelativo),
                'status' => 200,
                'ok' => true
            ]
        );
    }
}
