<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
date_default_timezone_set('America/Lima');

class DashboardController extends Controller
{
    public function index($fecha_ini, $fecha_fin) {
        $inicio = $fecha_ini!="null"?$fecha_ini:date("Y-m-01");
        $fin = $fecha_fin!="null"?$fecha_fin:date("Y-m-t");

        $sql_total_clientes = "SELECT COUNT(b.numerodocumento) AS cantidad 
            FROM creditos a JOIN clientes b ON a.cliente_id=b.id and b.estado=1 WHERE a.estado=1 
            AND a.empresa_id=? AND (a.fecha BETWEEN ? AND ?)";

        $data['total_cliente'] = DB::selectOne($sql_total_clientes, [
            auth()->user()->empresa_id,
            $inicio,
            $fin
        ]);

        /*$sql_monto_prestado = "SELECT SUM(a.monto) AS montoprestado
            FROM creditos a JOIN clientes b ON a.cliente_id=b.id and b.estado=1 WHERE a.estado IN (1, 2)
            AND a.empresa_id=? AND (a.fecha BETWEEN ? AND ?)";
        
        $data['total_prestado'] = DB::selectOne($sql_monto_prestado, [
            auth()->user()->empresa_id,
            $inicio,
            $fin
        ]);*/

        $sql_monto_cobrado = "SELECT ROUND(SUM(c.monto), 2) AS montocobrado
            FROM creditos a JOIN pagos c ON c.credito_id=a.id AND c.estado=1 
            WHERE a.estado IN (2, 3)
            AND a.empresa_id=? AND (a.fecha BETWEEN ? AND ?) AND (c.fecha BETWEEN ? AND ?)";
        
        $data['total_cobrado'] = DB::selectOne($sql_monto_cobrado, [
            auth()->user()->empresa_id,
            $inicio,
            $fin,
            $inicio,
            $fin
        ]);

        $sql_total_capital = "SELECT ROUND(SUM(a.monto), 2) AS montocapital
            FROM creditos a JOIN clientes b ON a.cliente_id=b.id and b.estado=1 WHERE a.estado IN (1, 2, 3)
            AND a.empresa_id=? AND (a.fecha BETWEEN ? AND ?)";

        $data['total_capital'] = DB::selectOne($sql_total_capital, [
            auth()->user()->empresa_id,
            $inicio,
            $fin
        ]);

        $sql_total_restante = "SELECT  ROUND(SUM(ROUND(t1.monto+t1.interes_socio+t1.interes_negocio, 2)), 2) AS total
                    FROM 
                    (
                           SELECT 
                            if(t.nro_mes>1.01, ROUND(((((ROUND((t.monto*(t.porcentaje/100)), 2) + t.monto)*(t.porcentajesocio/100))/t.nro_perio_calculado)*(t.nro_dias-30)), 2),
													 ROUND((((t.monto*(t.porcentajesocio/100))/t.nro_perio_calculado)*t.nro_dias), 2)) AS interes_socio,

                            if(t.nro_mes>1.01, ROUND(((((ROUND((t.monto*(t.porcentaje/100)), 2) + t.monto)*(t.porcentajenegocio/100))/t.nro_perio_calculado)*(t.nro_dias-30)), 2), 
													 ROUND((((t.monto*(t.porcentajenegocio/100))/t.nro_perio_calculado)*t.nro_dias), 2) ) AS interes_negocio,
                           
                            IF(t.nro_mes>1.01, (ROUND((t.monto*(t.porcentaje/100)), 2) + t.monto), t.monto) AS monto 
                            FROM 
                        (SELECT a.id, a.fecha, a.fechalimite, a.total, a.monto, a.descripcion_bien, a.codigocredito, a.codigocontrato    
                            ,b.numerodocumento, b.nombrescliente, c.tiposervicio,
                            DATEDIFF(CURDATE(),a.fecha) AS nro_dias,
                            ROUND((DATEDIFF(CURDATE(),a.fecha)/30), 2) AS nro_mes,
                            IF(c.periodo='DIAS', c.numeroperiodo, IF(periodo='SEMANAS', c.numeroperiodo * 7, IF(c.periodo='MES', c.numeroperiodo * 30, 0))) AS nro_perio_calculado,
                            c.porcentajesocio, c.porcentajenegocio, c.porcentaje
                            FROM creditos a 
                            JOIN clientes b ON a.cliente_id=b.id 
                            JOIN servicios c ON a.servicio_id=c.id 
                            WHERE a.estado IN (1,3) AND a.empresa_id=? 
                            and (a.fecha BETWEEN ? AND ? )
                        ) AS t
                    ) AS t1";
        $data['total_restante'] = DB::selectOne($sql_total_restante, [
            auth()->user()->empresa_id,
            $inicio,
            $fin
        ]);

        return response()->json(
            [
                'data' =>  $data,
                'status' => 201,
                'ok' => true
            ], 201
        );
    }
}
