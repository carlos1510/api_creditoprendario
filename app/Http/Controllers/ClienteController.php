<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Exception;

use GuzzleHttp\Client;
date_default_timezone_set('America/Lima');

class ClienteController extends Controller
{
    public function getByTipoDocumento($tipodocumento, $numerodocumento) {
        $data = array('tipodocumento' => $tipodocumento, 'numerodocumento' => $numerodocumento, 'nombrescliente' => '');
        $cliente = Cliente::where('tipodocumento', $tipodocumento)
                ->where('numerodocumento', $numerodocumento)
                ->where('empresa_id', auth()->user()->empresa_id)
                ->first();

        if(!isset($cliente->id)){
            if($tipodocumento == 1){
                //DNI
                //consultaremos a la api de padron persona
                $token = 'apis-token-866.a7kD7Q9DNmGj1NG1uYFqp1PxnGB8zpjd';

                $client = new Client(['base_uri' => 'https://api.apis.net.pe', 'verify' => false]);
                $parameters = [
                    'http_errors' => false,
                    'connect_timeout' => 5,
                    'headers' => [
                        'Authorization' => 'Bearer '.$token,
                        'Referer' => 'https://apis.net.pe/api-consulta-dni',
                        'User-Agent' => 'laravel/guzzle',
                        'Accept' => 'application/json',
                    ],
                    'query' => ['numero' => $numerodocumento]
                ];
                $res = $client->request('GET', '/v1/dni', $parameters);
                $resultado = json_decode($res->getBody()->getContents(), true);
                $data = array('tipodocumento' => $tipodocumento, 'numerodocumento' => $numerodocumento, 'nombrescliente' => $resultado['nombres'].' '.$resultado['apellidoPaterno'].' '.$resultado['apellidoMaterno']);
            }
        }

        return response()->json([
            'data' => isset($cliente->id)?$cliente:$data, 
            'status' => 200,
            'ok' => true
        ], 200);
    }
}
