<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isNull;

class ClienteController extends Controller
{
    public function getByTipoDocumento($tipodocumento, $numerodocumento) {
        $cliente = Cliente::where('tipodocumento', $tipodocumento)
            ->where('numerodocumento', $numerodocumento)
            ->first();

        if(isNull($cliente->id)){
            if($tipodocumento == 1){
                //DNI
                //consultaremos a la api de padron persona
            }
        }

        return response()->json($cliente);
    }
}
