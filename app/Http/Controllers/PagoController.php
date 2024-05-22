<?php

namespace App\Http\Controllers;

use App\Http\Utils\Util;
use App\Models\Credito;
use App\Models\Pago;
use App\Models\TipoComprobante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
date_default_timezone_set('America/Lima');

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
        $pago->seriecorrelativo = $request->seriecorrelativo;
        $pago->numerocorrelativo = $request->numerocorrelativo;
        $pago->codigogenerado = $request->codigogenerado;
        $pago->numeropago = $request->numeropago;
        $pago->codigopago = $request->codigopago;
        $pago->fecha = $request->fecha;
        $pago->fechavencimientoanterior = Util::convertirStringFecha($request->fechavencimientoanterior, false);
        $pago->codigocredito = $request->codigocredito;
        $pago->codigocontrato = $request->codigocontrato;
        $pago->capital = $request->capital;
        $pago->interes = $request->interes;
        $pago->interes_socio = $request->interes_socio;
        $pago->igv = $request->igv;
        $pago->totalinteressocio = $request->totalinteressocio;
        $pago->interes_negocio = $request->interes_negocio;
        $pago->total = $request->total;
        $pago->monto = $request->monto;
        $pago->montorestante = isset($request->montorestante)?$request->montorestante:0;
        $pago->nro_dias = $request->nro_dias;
        $pago->tiposervicio = isset($request->tiposervicio)?$request->tiposervicio:null;
        
        $pago->plazo = $request->plazo;
        
        $pago->estado = 1;
        $pago->tipo_comprobante_id = $request->tipo_comprobante_id;
        //$pago->user_id = $request->user()->id;
        $pago->user_id = $request->user_id;
        $pago->empresa_id = $request->empresa_id;
        $pago->credito_id = $request->credito_id;

        $pago->nuevocapital = $pago->montorestante;
        
        if($request->montorestante == "0.00"){
            //se cancela el contrato
            $credito = Credito::find($pago->credito_id);
            $credito->estado = 2;
            $credito->estados = 'CANCELACIÓN CONTRATO';
            $credito->update();
            $pago->fechavencimientonuevo = null;
        }else{
            //se renueva el contrato
            $credito = Credito::find($pago->credito_id);
            $credito->monto = $pago->montorestante;
            $credito->fecha = $pago->fecha;
            $credito->fechalimite = date("Y-m-d",strtotime($pago->fecha."+ 31 days"));
            $credito->estado = 3;
            $credito->estados = 'RENOVACION CONTRATO';
            $credito->update();
            $pago->fechavencimientonuevo = date("Y-m-d",strtotime($pago->fecha."+ 31 days"));
        }

        $pago->save();

        return response()->json(
            [
                'data' =>  $this->prepararImprimirPago($pago->id),
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

    public function getUltimoNroComprobante($tipoComprobanteID, Request $request){
        $nroComprobante = Pago::selectRaw("IF(ISNULL(MAX(numerocorrelativo)), 0, MAX(numerocorrelativo)) AS numero")
            ->where('tipo_comprobante_id', $tipoComprobanteID)
            ->first();

        $nroSerie = Pago::selectRaw("IF(ISNULL(MAX(seriecorrelativo)), 0,MAX(seriecorrelativo)) as serie")
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

    public function getUltimoNroPago(){
        $nroPago = Pago::selectRaw("IF(ISNULL(MAX(numeropago)), 0, MAX(numeropago)) AS numero")
            ->where('empresa_id', 1)
            ->first();

        $codigopago = sprintf("%010d", ($nroPago->numero + 1));
        
        $numeropago = $nroPago->numero + 1;

        return response()->json(
            [
                'data' => array('numeropago' => $numeropago, 'codigopago' => $codigopago),
                'status' => 200,
                'ok' => true
            ]
        );
    }

    public function prepararImprimirPago($id){
        $pago = Pago::select('pagos.fecha','pagos.monto','pagos.interes','pagos.capital','pagos.total','pagos.fechavencimientoanterior',
        'pagos.codigocredito', 'pagos.codigocontrato', 'pagos.interes_socio', 'pagos.igv', 'pagos.codigogenerado', 'pagos.codigopago', 
        'pagos.montorestante', 'pagos.totalinteressocio', 'pagos.interes_negocio', 'pagos.nro_dias', 'pagos.tiposervicio', 'pagos.nuevocapital',
        'pagos.plazo', 'pagos.fechavencimientonuevo', 
        'a.descripcion_bien','b.nombre AS nombre_empresa', 'b.nombrenegocio','b.direccion AS direccion_empresa',
        'e.nombre AS nom_tipo_comprobante',"c.nombres AS nombres_cajero",
        'b.numerodocumento AS nrodoc_empresa', 'nombrescliente','d.numerodocumento AS nrodoc_cliente')
        ->selectRaw("DATE_FORMAT(pagos.created_at, '%H:%i:%s') AS hora")
        ->selectRaw(" IF(a.tipo_comprobante_id=1,'DNI','RUC') AS descripcion_tipo_doc_empresa")
        ->join('creditos AS a','pagos.credito_id','=','a.id')
        ->join('tipo_comprobantes AS e','pagos.tipo_comprobante_id','=','e.id')
        ->join('servicios AS f', 'a.servicio_id','=','f.id')
        ->join('empresas AS b','pagos.empresa_id','=','b.id')
        ->join('users AS c','pagos.user_id','=','c.id')
        ->join('clientes AS d','a.cliente_id','=','d.id')
        ->where('pagos.estado',1)
        ->where('pagos.id', $id)
        ->first();

        return $pago;
    }
}
