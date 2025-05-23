<?php

namespace App\Http\Controllers;

use App\Http\Utils\Util;
use App\Models\Cliente;
use App\Models\Credito;
use App\Models\DetalleCredito;
use App\Models\TipoComprobante;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
date_default_timezone_set('America/Lima');

class CreditoController extends Controller
{
    public function index($responsableId, $fecha_ini, $fecha_fin, $nro_documento,Request $request) {
        if($responsableId==0 && $fecha_ini=="null" && $fecha_fin=="null" && $nro_documento==0){
            $inicio = $fecha_ini!="null"?$fecha_ini:date("Y-m-01");
            $fin = $fecha_fin!="null"?$fecha_fin:date("Y-m-t");
            $creditos = Credito::select('creditos.id', 'creditos.fecha', 'creditos.fechalimite', 'creditos.seriecorrelativo', 'creditos.numerocorrelativo', 'creditos.codigogenerado', 'creditos.tipomoneda', 'creditos.descripcion_bien'
            ,'creditos.igv', 'creditos.monto', 'creditos.interes', 'creditos.subtotal', 'creditos.total', 'creditos.total_texto', 'creditos.descuento', 'creditos.montoactual', 'creditos.estados', 'creditos.user_id', 'creditos.cliente_id',
            'creditos.tipo_comprobante_id', 'creditos.servicio_id', 'b.tipodocumento', 'b.numerodocumento', 'b.nombrescliente', 'b.direccion', 'b.referencia', 'b.telefono1', 'b.telefono2'
            , 'b.email', 'c.tiposervicio', 'd.nombre as nombre_comprobante', 'creditos.numerocredito', 'creditos.codigocredito', 'creditos.numerocontrato', 'creditos.codigocontrato')
            ->join('clientes as b','creditos.cliente_id', '=','b.id')
            ->join('servicios as c','creditos.servicio_id', '=','c.id')
            ->join('tipo_comprobantes as d','creditos.tipo_comprobante_id','=','d.id')
            ->whereIn('creditos.estado', [1,3])
            ->where('creditos.empresa_id', auth()->user()->empresa_id)
            ->whereBetween('creditos.fecha', [$inicio, $fin])
            ->orderBy('creditos.fecha','desc')
            ->get();
        }else{
            $sql = "SELECT a.id, a.fecha, a.fechalimite, a.seriecorrelativo, a.numerocorrelativo, a.codigogenerado, a.tipomoneda, a.descripcion_bien
                ,a.igv, a.monto, a.interes, a.subtotal, a.total, a.total_texto, a.descuento, a.montoactual, a.estados, a.user_id, a.cliente_id,
                a.tipo_comprobante_id, a.servicio_id, b.tipodocumento, b.numerodocumento, b.nombrescliente, b.direccion, b.referencia, b.telefono1, b.telefono2
                , b.email, c.tiposervicio, d.nombre as nombre_comprobante, a.numerocredito, a.codigocredito, a.numerocontrato, a.codigocontrato 
                FROM creditos a JOIN clientes b ON a.cliente_id=b.id 
                JOIN servicios c on a.servicio_id=c.id 
                JOIN tipo_comprobantes d ON a.tipo_comprobante_id=d.id
                WHERE a.estado IN (1,3) AND a.empresa_id=".auth()->user()->empresa_id."
                ".(isset($fecha_ini)?($fecha_ini!="null"?(isset($fecha_fin)?($fecha_fin!="null"?" AND a.fecha BETWEEN '$fecha_ini' AND '$fecha_fin' ":""):""):""):"").
                (isset($responsableId)?($responsableId!=0?" AND a.user_id=$responsableId ":""):"").
                (isset($nro_documento)?($nro_documento!=0?" AND b.numerodocumento='$nro_documento'":""):"").
                " ORDER BY a.fecha desc";
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

    public function show($nro_documento, Request $request) {
        if($nro_documento != ""){
            $sql = "SELECT t1.id, t1.fecha, t1.fechalimite, t1.monto, t1.descripcion_bien, t1.numerodocumento, t1.nombrescliente, 
                        t1.interes_socio, t1.interes_negocio, t1.nro_dias, t1.tiposervicio, t1.codigocontrato, t1.codigocredito, t1.plazo,
                        ROUND((t1.interes_socio * 0.18), 2) AS igv, DATEDIFF(t1.fechalimite, CURDATE()) AS intervalo 
                    FROM (
                        SELECT t.id, t.fecha, t.fechalimite, t.descripcion_bien, t.numerodocumento, t.nombrescliente, 
                            t.nro_dias, t.codigocontrato, t.codigocredito, t.tiposervicio,
                            CONCAT(t.nro_perio_calculado, ' días') AS plazo,

                            -- Limitar el cálculo del interes_socio a un máximo de 60 días (2.01 meses)
                            IF(
                                t.nro_mes > 2.01, 
                                ROUND(((((ROUND((t.monto * (t.porcentaje / 100)), 2) + t.monto) * (t.porcentajesocio / 100)) / t.nro_perio_calculado) * 60), 2),
                                IF(
                                    t.nro_mes > 1.01, 
                                    ROUND(((((ROUND((t.monto * (t.porcentaje / 100)), 2) + t.monto) * (t.porcentajesocio / 100)) / t.nro_perio_calculado) * (t.nro_dias - 30)), 2),
                                    ROUND((((t.monto * (t.porcentajesocio / 100)) / t.nro_perio_calculado) * t.nro_dias), 2)
                                )
                            ) AS interes_socio,

                            -- Limitar el cálculo del interes_negocio a un máximo de 60 días (2.01 meses)
                            IF(
                                t.nro_mes > 2.01,
                                ROUND(((((ROUND((t.monto * (t.porcentaje / 100)), 2) + t.monto) * (t.porcentajenegocio / 100)) / t.nro_perio_calculado) * 60), 2),
                                IF(
                                    t.nro_mes > 1.01,
                                    ROUND(((((ROUND((t.monto * (t.porcentaje / 100)), 2) + t.monto) * (t.porcentajenegocio / 100)) / t.nro_perio_calculado) * (t.nro_dias - 30)), 2),
                                    ROUND((((t.monto * (t.porcentajenegocio / 100)) / t.nro_perio_calculado) * t.nro_dias), 2)
                                )
                            ) AS interes_negocio,

                            -- Limitar el cálculo del monto a un máximo de 60 días (2.01 meses)
                            IF(
                                t.nro_mes > 2.01,
                                ROUND((t.monto * (t.porcentaje / 100)), 2) + t.monto,
                                IF(
                                    t.nro_mes > 1.01,
                                    (ROUND((t.monto * (t.porcentaje / 100)), 2) + t.monto),
                                    t.monto
                                )
                            ) AS monto 
                        FROM (
                            SELECT a.id, a.fecha, a.fechalimite, a.total, a.monto, a.descripcion_bien, a.codigocredito, a.codigocontrato,    
                                b.numerodocumento, b.nombrescliente, c.tiposervicio,
                                DATEDIFF(CURDATE(), a.fecha) AS nro_dias,
                                ROUND((DATEDIFF(CURDATE(), a.fecha) / 30), 2) AS nro_mes,
                                IF(c.periodo = 'DIAS', c.numeroperiodo, 
                                    IF(c.periodo = 'SEMANAS', c.numeroperiodo * 7, 
                                        IF(c.periodo = 'MES', c.numeroperiodo * 30, 0))) AS nro_perio_calculado,
                                c.porcentajesocio, c.porcentajenegocio, c.porcentaje
                            FROM creditos a 
                            JOIN clientes b ON a.cliente_id = b.id 
                            JOIN servicios c ON a.servicio_id = c.id 
                            WHERE a.estado IN (1, 3) AND a.empresa_id=".auth()->user()->empresa_id." and b.numerodocumento='$nro_documento' 
                        ) AS t
                    ) AS t1;";
            $creditos = DB::select($sql);
        }
        /**
         * SELECT IF(
		*ROUND((DATEDIFF(CURDATE(),'2025-03-12')/30), 2) > 1.01 
		*AND 
		*ROUND((DATEDIFF(CURDATE(),'2025-03-12')/30), 2) < 2.01, 
		*'verdadero', 
		*'falso'
	    *)
         */

        return response()->json(
            [
                'data' =>  isset($creditos)?$creditos:[],
                'status' => 200,
                'ok' => true
            ], 200
        );
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'fechalimite' => 'required',
            'codigogenerado' => 'required',
            'tipomoneda' => 'required',
            'monto' => 'required',
            'total_texto' => 'required',
            'tipodocumento' => 'required',
            'numerodocumento' => 'required',
            'nombrescliente' => 'required',
            'codigocredito' => 'required',
            'codigocontrato' => 'required'
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
            $cliente->empresa_id = auth()->user()->empresa_id;
            $cliente->user_id = auth()->user()->id;
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
        $credito->numerocredito = $request->numerocredito;
        $credito->codigocredito = $request->codigocredito;
        $credito->numerocontrato = $request->numerocontrato;
        $credito->codigocontrato = $request->codigocontrato;
        $credito->tipomoneda = $request->tipomoneda;
        $credito->descripcion_bien = isset($request->descripcion_bien)?$request->descripcion_bien:null;
        $credito->igv = $request->igv;
        $credito->monto = $request->monto;
        $credito->interes = $request->interes;
        $credito->subtotal = $request->subtotal;
        $credito->total = $request->total;
        $credito->total_texto = $request->total_texto;
        $credito->descuento = $request->descuento;
        $credito->estados = 'ACTIVO';
        $credito->estado = 1;
        $credito->user_id = auth()->user()->id;
        $credito->tipo_comprobante_id = $request->tipo_comprobante_id;
        $credito->cliente_id = $request->cliente_id;
        $credito->servicio_id = $request->servicio_id;
        $credito->empresa_id = auth()->user()->empresa_id;
        $credito->save();

        if($request->servicio_id != 4){
            foreach($request->detalle as $item){
                $item = (object)$item;
    
                $detalle = new DetalleCredito();
                $detalle->descripcion = $item->descripcion;
                $detalle->valor1 = $item->valor1;
                $detalle->valor2 = $item->valor2;
                $detalle->valor3 = $item->valor3;
                $detalle->observaciones = $item->observaciones;
                $detalle->valorizacion = $item->valorizacion;
                $detalle->estado = 1;
                $detalle->credito_id = $credito->id;
                $detalle->servicio_id = $request->servicio_id;
    
                $detalle->save();
            }
        }else{
            $detalle = new DetalleCredito();
                $detalle->descripcion = "PRESTAMO EFECTIVO";
                $detalle->valorizacion = $request->monto;
                $detalle->estado = 1;
                $detalle->credito_id = $credito->id;
                $detalle->servicio_id = $request->servicio_id;
    
                $detalle->save();
        }
        

        return response()->json(
            [
                'data' =>  $this->prepararImprimirCredito($credito->id),
                'status' => 201,
                'ok' => true
            ],201
        );
    }

    public function update($id, Request $request){
        $credito = Credito::find($id);

        $validator = Validator::make($request->all(), [
            'fecha' => 'required',
            'fechalimite' => 'required',
            'codigogenerado' => 'required',
            'tipomoneda' => 'required',
            'monto' => 'required',
            'total_texto' => 'required',
            'tipodocumento' => 'required',
            'numerodocumento' => 'required',
            'nombrescliente' => 'required',
            'codigocredito' => 'required',
            'codigocontrato' => 'required'
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

        $credito->fecha = $request->fecha;
        $credito->fechalimite = Util::convertirStringFecha($request->fechalimite, false);
        $credito->seriecorrelativo = $request->seriecorrelativo;
        $credito->numerocorrelativo = $request->numerocorrelativo;
        $credito->codigogenerado = $request->codigogenerado;
        $credito->numerocredito = $request->numerocredito;
        $credito->codigocredito = $request->codigocredito;
        $credito->numerocontrato = $request->numerocontrato;
        $credito->codigocontrato = $request->codigocontrato;
        $credito->tipomoneda = $request->tipomoneda;
        $credito->descripcion_bien = isset($request->descripcion_bien)?$request->descripcion_bien:null;
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

        foreach($request->detalle as $resgistro){
            $resgistro = (object)$resgistro;
            if(isset($resgistro->id)){
                $detalle = DetalleCredito::find($resgistro->id);
                $detalle->descripcion = $resgistro->descripcion;
                $detalle->valor1 = $resgistro->valor1;
                $detalle->valor2 = $resgistro->valor2;
                $detalle->valor3 = $resgistro->valor3;
                $detalle->observaciones = $resgistro->observaciones;
                $detalle->valorizacion = $resgistro->valorizacion;

                $detalle->update();
            }else{
                $detalle = new DetalleCredito();
                $detalle->descripcion = $resgistro->descripcion;
                $detalle->valor1 = $resgistro->valor1;
                $detalle->valor2 = $resgistro->valor2;
                $detalle->valor3 = $resgistro->valor3;
                $detalle->observaciones = $resgistro->observaciones;
                $detalle->valorizacion = $resgistro->valorizacion;
                $detalle->estado = 1;
                $detalle->credito_id = $credito->id;
                $detalle->servicio_id = $request->servicio_id;

                $detalle->save();
            }
        }

        return response()->json(
            [
                'data' =>  $credito,
                'status' => 201,
                'ok' => true
            ],201
        );
    }

    public function destroy($id) {
        $credito = Credito::find($id);

        $credito->estados = 'INACTIVO';
        $credito->estado = 0;

        $credito->update();

        return response()->json(
            [
                'data' =>  $credito,
                'status' => 201,
                'ok' => true
            ], 201
        );
    }

    public function getUltimoNroComprobante($tipoComprobanteID, Request $request){
        $nroComprobante = Credito::selectRaw("IF(ISNULL(MAX(numerocorrelativo)), 0, MAX(numerocorrelativo)) AS numero")
            ->where('tipo_comprobante_id', $tipoComprobanteID)
            ->where('empresa_id', auth()->user()->empresa_id)
            ->first();

        $nroSerie = Credito::selectRaw("IF(ISNULL(MAX(seriecorrelativo)), 0,MAX(seriecorrelativo)) as serie")
        ->where('tipo_comprobante_id', $tipoComprobanteID)
        ->where('empresa_id', auth()->user()->empresa_id)
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
            ], 200
        );
    }

    public function getUltimoNroCreditoContrato(){
        $nroCredito = Credito::selectRaw("IF(ISNULL(MAX(numerocredito)), 0, MAX(numerocredito)) AS numero")
            ->where('empresa_id', auth()->user()->empresa_id)
            ->first();

        $nroContrato = Credito::selectRaw("IF(ISNULL(MAX(numerocontrato)), 0,MAX(numerocontrato)) as numero")
        ->where('empresa_id', 1)
        ->first();

        $codigocredito = sprintf("%010d", ($nroCredito->numero + 1));
        $codigocontrato = sprintf("%010d", ($nroContrato->numero + 1));
        $numerocredito = $nroCredito->numero + 1;
        $numerocontrato = $nroContrato->numero + 1;

        return response()->json(
            [
                'data' => array('numerocredito' => $numerocredito, 'codigocredito' => $codigocredito, 'numerocontrato' => $numerocontrato, 'codigocontrato' => $codigocontrato),
                'status' => 200,
                'ok' => true
            ], 200
        );
    }

    public function prepararImprimirCredito($id){
        $pago = Credito::select('d.nombre AS nombre_empresa', 'd.direccion AS direccion_empresa', 'd.numerodocumento AS nrodoc_empresa', 'creditos.codigocredito',
        'creditos.monto', 'creditos.fechalimite', 'creditos.codigogenerado', 'b.nombre AS nom_tipo_comprobante', 'd.nombrenegocio', 'creditos.codigocontrato','creditos.servicio_id',
        'c.tiposervicio', 'e.nombres AS nombres_cajero', 'f.nombrescliente', 'f.numerodocumento AS nrodoc_cliente','f.direccion AS direccioncliente','creditos.fecha',
        'd.razonsocial', 'd.razonsocialsocio')
        ->selectRaw("DATE_FORMAT(creditos.created_at, '%H:%i:%s') AS hora")
        ->selectRaw("IF(creditos.tipo_comprobante_id=1,'DNI','RUC') AS descripcion_tipo_doc_empresa")
        ->selectRaw("CONCAT(IF(c.periodo='MES', (30*c.numeroperiodo), 0), ' Dias') AS plazo")
        ->selectRaw("IF(c.periodo='MES', 'Mensual', '') AS formapago")
        ->selectRaw("ROUND((creditos.monto*c.porcentajenegocio)/100,2) AS interesnegocio")
        ->selectRaw("ROUND((creditos.monto*c.porcentajesocio)/100,2) AS interessocio")
        ->selectRaw("ROUND((creditos.monto*0.10),2) AS pagominimo")
        ->selectRaw("DATE_FORMAT(NOW(), '%d/%m/%Y %h:%i') AS fechahoraactual")
        ->join('tipo_comprobantes AS b','creditos.tipo_comprobante_id','=','b.id')
        ->join('servicios AS c','creditos.servicio_id','=','c.id')
        ->join('empresas AS d', 'creditos.empresa_id','=','d.id')
        ->join('users AS e','creditos.user_id','=','e.id')
        ->join('clientes AS f','creditos.cliente_id','=','f.id')
        ->whereIn('creditos.estado', array(1,3))
        ->where('creditos.id', $id)
        ->where('creditos.empresa_id', auth()->user()->empresa_id)
        ->first();

        $detalles = DetalleCredito::where('estado',1)
        ->where('credito_id',$id)
        ->get();

        return ['pago'=>$pago, 'detalles'=>$detalles];
    }
}
