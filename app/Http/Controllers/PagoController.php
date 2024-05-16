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

        /*if($pago->total == $pago->monto){
            //se termino el pago
            $credito = Credito::find($pago->credito_id);
            $credito->estado = 2;
            $credito->estados = 'PAGADO';
            $credito->update();
        }*/

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

    protected function prepararImprimirPago($id){
        $pago = Pago::select('pagos.fecha','pagos.monto','pagos.interes','pagos.capital','pagos.total','a.fechalimite',
        'a.codigogenerado','a.descripcion_bien','f.tiposervicio','b.nombre AS nombre_empresa','b.direccion AS direccion_empresa',
        'e.nombre AS nom_tipo_comprobante',"c.nombres AS nombres_cajero",
        'b.numerodocumento AS nrodoc_empresa', 'nombrescliente','d.numerodocumento AS nrodoc_cliente')
        ->selectRaw("DATE_FORMAT(pagos.created_at, '%H:%i:%s') AS hora")
        ->selectRaw(" IF(a.tipo_comprobante_id=1,'DNI','RUC') AS descripcion_tipo_doc_empresa")
        ->join('creditos AS a','pagos.credito_id','=','a.id')
        ->join('tipo_comprobantes AS e','a.tipo_comprobante_id','=','e.id')
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
