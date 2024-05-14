<?php

namespace App\Http\Controllers;

use App\Http\Utils\Util;
use App\Models\Caja;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index(Request $request) {
        $cajas = Caja::all()->where('estado', 1);

        return response()->json($cajas, 200);
    }

    public function indexFilter($fecha_ini, $fecha_fin, Request $request){
        $inicio = $fecha_ini!="null"?$fecha_ini:date("Y-m-01");
        $fin = $fecha_fin!="null"?$fecha_fin:date("Y-m-t");

        $cajas = Caja::select("cajas.id", "cajas.fechaapertura", "cajas.horaapertura","cajas.montoinicial",
        "cajas.fechacierre", "cajas.horacierre","cajas.montocobro","cajas.montocredito","cajas.montogasto",
        "cajas.montocierre","cajas.estado", "cajas.user_id", "cajas.empresa_id", "b.numerodocumento",
        "b.nombres", "b.apellidos")
        ->join("users as b","cajas.user_id","=","b.id")
            ->whereIn('estado', [1,2])
            ->whereBetween('fechaapertura', [$inicio, $fin])
            ->get();

        return response()->json(
            [
                'data' => $cajas,
                'status' => 200,
                'message' => 'Servicios obtenidos correctamente'
            ]
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
        $caja->estado = 1;
        $caja->user_id = $request->user_id;
        $caja->empresa_id = $request->empresa_id;

        $caja->save();

        return response()->json([
            'data' => $caja, 
            'status' => 201,
            'ok' => true
        ]);
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
        $caja->user_id = $request->user_id;

        $caja->update();

        return response()->json([
            'data' => $caja, 
            'status' => 201,
            'ok' => true
        ]);
    }

    public function cerrarCaja(Request $request) {
        $caja = Caja::find($request->id);

        $caja->fechacierre = Util::convertirStringFecha($request->fechacierre, false);
        $caja->horacierre = $request->horacierre;
        $caja->montocierre = $request->montocierre;
        $caja->montocredito = $request->montocredito;
        $caja->montogasto = isset($request->montogasto)?$request->montogasto:0;
        $caja->montocobro = $request->montocobro;

        $caja->estado = 2; //caja cerrado

        $caja->update();

        return response()->json([
            'data' => $caja, 
            'status' => 201,
            'ok' => true
        ]);
    }

    public function getCerrarCaja($id, Request $request) {
        $caja = Caja::find($id);

        $fecha_actual = date('Y-m-d');

        $sql_pago = "SELECT IFNULL(SUM(a.monto),0) AS totalpagos 
            FROM pagos a JOIN creditos b ON a.credito_id=b.cliente_id 
            WHERE a.estado=1 AND (a.fecha between '$caja->fechaapertura' AND '$fecha_actual')
            AND a.empresa_id='$caja->empresa_id' AND a.user_id=$caja->user_id";
        $result_pago = DB::selectOne($sql_pago);

        $sql_credito = "SELECT IFNULL(SUM(a.total),0) AS total_prestamo 
            FROM creditos a 
            WHERE a.estado=1 AND (a.fecha BETWEEN '$caja->fechaapertura' AND '$fecha_actual') AND a.empresa_id='$caja->empresa_id' AND a.user_id=$caja->user_id";
        $result_credito = DB::selectOne($sql_credito);

        /*$sql_gasto = "";
        $result_gasto = DB::selectOne($sql_gasto);*/

        $monto_cierre = ((double)$caja->montoinicial + (double)$result_pago->totalpagos) - (double)$result_credito->total_prestamo;

        $caja->montocierre = $monto_cierre;
        $caja->montocredito = $result_credito->total_prestamo;
        $caja->montogasto = 0;
        $caja->montocobro = $result_pago->totalpagos;

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
}
