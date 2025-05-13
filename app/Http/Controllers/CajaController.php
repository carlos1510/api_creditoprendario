<?php

namespace App\Http\Controllers;

use App\Http\Utils\Util;
use App\Models\Caja;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
date_default_timezone_set('America/Lima');

class CajaController extends Controller
{
    public function index(Request $request) {
        if(auth()->user()->rol != "Administrador"){
            $cajas = Caja::where('estado', 1)
            ->where('empresa_id', auth()->user()->empresa_id)->get();
        }else {
            $cajas = Caja::where('estado', 1)->get();
        }

        return response()->json([
            'data' => $cajas, 
            'status' => 200,
            'ok' => true
        ],200);
    }

    public function indexFilter($fecha_ini, $fecha_fin, Request $request){
        $inicio = $fecha_ini!="null"?$fecha_ini:date("Y-m-01");
        $fin = $fecha_fin!="null"?$fecha_fin:date("Y-m-t");

        if(auth()->user()->rol != "Administrador"){
            $cajas = Caja::select("cajas.id", "cajas.fechaapertura", "cajas.horaapertura","cajas.montoinicial",
            "cajas.fechacierre", "cajas.horacierre","cajas.montocobro","cajas.montocredito","cajas.montogasto",
            "cajas.montocierre","cajas.estado", "cajas.user_id", "cajas.empresa_id", "b.numerodocumento",
            "b.nombres", "b.apellidos", "cajas.interessocio")
            ->join("users as b","cajas.user_id","=","b.id")
                ->whereIn('estado', [1,2])
                ->where('cajas.empresa_id', auth()->user()->empresa_id)
                ->whereBetween('fechaapertura', [$inicio, $fin])
                ->orderBy('cajas.created_at','desc')
                ->get();
        }else {
            $cajas = Caja::select("cajas.id", "cajas.fechaapertura", "cajas.horaapertura","cajas.montoinicial",
            "cajas.fechacierre", "cajas.horacierre","cajas.montocobro","cajas.montocredito","cajas.montogasto",
            "cajas.montocierre","cajas.estado", "cajas.user_id", "cajas.empresa_id", "b.numerodocumento",
            "b.nombres", "b.apellidos", "cajas.interessocio")
            ->join("users as b","cajas.user_id","=","b.id")
                ->whereIn('estado', [1,2])
                ->whereBetween('fechaapertura', [$inicio, $fin])
                ->orderBy('cajas.created_at','desc')
                ->get();
        }

        return response()->json(
            [
                'data' => $cajas,
                'status' => 200,
                'ok' => true,
                'message' => 'Servicios obtenidos correctamente'
            ], 200
        );
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
        $caja->fechaapertura = Util::convertirStringFecha($request->fechaapertura, false);
        $caja->horaapertura = $request->horaapertura;
        $caja->montoinicial = $request->montoinicial;
        $caja->fechacierre = null;
        $caja->horacierre = null;
        $caja->montocierre = null;
        $caja->montocredito = null;
        $caja->montogasto = null;
        $caja->montocobro = null;
        $caja->totalcapital = null;
        $caja->interessocio = null;
        $caja->interesnegocio = null;
        $caja->estado = 1;
        $caja->user_id = $request->user_id;
        $caja->empresa_id = auth()->user()->empresa_id;

        $caja->save();

        return response()->json([
            'data' => $caja, 
            'status' => 201,
            'ok' => true
        ], 201);
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

        $caja->fechaapertura = Util::convertirStringFecha($request->fechaapertura, false);
        $caja->horaapertura = $request->horaapertura;
        $caja->montoinicial = $request->montoinicial;
        $caja->user_id = isset($request->user_id)?$request->user_id:auth()->user()->id;

        $caja->update();

        return response()->json([
            'data' => $caja, 
            'status' => 201,
            'ok' => true
        ], 201);
    }

    public function cerrarCaja(Request $request) {
        $caja = Caja::find($request->id);

        $caja->fechacierre = Util::convertirStringFecha($request->fechacierre, false);
        $caja->horacierre = $request->horacierre;
        $caja->montocierre = $request->montocierre;
        $caja->montocredito = $request->montocredito;
        $caja->montogasto = isset($request->montogasto)?$request->montogasto:0;
        $caja->montocobro = $request->montocobro;
        $caja->totalcapital = $request->totalcapital;
        $caja->interessocio = $request->interessocio;
        $caja->interesnegocio = $request->interesnegocio;

        $caja->estado = 2; //caja cerrado

        $caja->update();

        return response()->json([
            'data' => $caja, 
            'status' => 201,
            'ok' => true
        ], 201);
    }

    public function getCerrarCaja($id, Request $request) {
        $caja = Caja::find($id);

        $fecha_actual = date('Y-m-d');

        $sql_pago = "SELECT IFNULL(SUM(a.monto),0) AS totalpagos, 
            ROUND((IFNULL(SUM(a.monto),0) - IFNULL(SUM(a.totalinteressocio), 0)), 2) AS totalcapital,
            ROUND(IFNULL(SUM(a.totalinteressocio), 0), 2) as interessocio, 
            ROUND(IFNULL(SUM(a.interes_negocio), 0), 2) AS interesnegocio 
            FROM pagos a JOIN creditos b ON a.credito_id=b.id  
            WHERE a.estado=1 AND (a.fecha between '$caja->fechaapertura' AND '$fecha_actual')
            AND a.empresa_id='".auth()->user()->empresa_id."' AND a.user_id=$caja->user_id";
        $result_pago = DB::selectOne($sql_pago);

        $sql_credito = "SELECT IFNULL(SUM(a.monto),0) AS total_prestamo 
            FROM creditos a 
            WHERE a.estado=1 AND (a.fecha BETWEEN '$caja->fechaapertura' AND '$fecha_actual') AND a.empresa_id='".auth()->user()->empresa_id."' AND a.user_id=$caja->user_id";
        $result_credito = DB::selectOne($sql_credito);

        $sql_gasto = "SELECT IFNULL(SUM(monto),0) AS total_gastos
            FROM gastos
            WHERE estado=1
            AND (fecha BETWEEN '$caja->fechaapertura' AND '$fecha_actual')
            AND empresa_id='".auth()->user()->empresa_id."'
            AND user_id=$caja->user_id";
        $result_gasto = DB::selectOne($sql_gasto);

        $monto_cierre = ((double)$caja->montoinicial + (double)$result_pago->totalpagos) - ((double)$result_credito->total_prestamo + (double)$result_gasto->total_gastos);

        $caja->montocierre = $monto_cierre;
        $caja->montocredito = $result_credito->total_prestamo;
        $caja->montogasto = $result_gasto->total_gastos;
        $caja->montocobro = $result_pago->totalpagos;
        $caja->totalcapital = $result_pago->totalcapital;
        $caja->interessocio = $result_pago->interessocio;
        $caja->interesnegocio = $result_pago->interesnegocio;

        return response()->json([
            'data' => $caja, 
            'status' => 201,
            'ok' => true
        ]);
    }

    public function destroy($id){
        $caja = Caja::find($id);
        $caja->estado = 0; //estado eliminado
        
        $caja->update();

        return response()->json([
            'data' => $caja, 
            'status' => 201,
            'ok' => true
        ]);
    }

    public function getAperturaCaja(Request $request) {
        $caja = Caja::selectRaw('DATEDIFF(CURDATE(),fechaapertura) as apertura_activo')
        ->where('estado', 1)
        ->where('user_id',auth()->user()->id)
        ->where('empresa_id',auth()->user()->empresa_id)
        ->first();

        return response()->json([
            'data' => is_null($caja)?array('apertura_activo'=>-1):$caja, 
            'status' => 201,
            'ok' => true
        ]);
    }

    public function getUltimoMontoCierre($iduser, Request $request) {
        $caja = Caja::select("montocierre")
        ->where('user_id', $iduser)
        ->where('empresa_id', auth()->user()->empresa_id)
        ->orderBy('created_at', 'desc')
        ->first();

        return response()->json([
            'data' => $caja, 
            'status' => 200,
            'ok' => true
        ]);
    }
}
