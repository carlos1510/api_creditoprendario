<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empresa;

class EmpresaController extends Controller
{
    public function index(Request $request) {
        $empresas = Empresa::all()->where('estado', 1);

        return response()->json($empresas, 200);
    }

    public function show($id, Request $request) {
        $empresa = Empresa::find($id);

        return response()->json($empresa, 200);
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'numerodocumento' => 'required',
            'tipomoneda' => 'required',
            'simbolomoneda' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $empresa = new Empresa();
        $empresa->nombre = $request->nombre;
        $empresa->numerodocumento = $request->numerodocumento;
        $empresa->email = $request->email;
        $empresa->direccion = $request->direccion;
        $empresa->telefono = $request->telefono;
        if($request->has('image') && $request->filled('image')){
            //Decodificar la imagen de base64
            $image = $request->get('image');
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ','+',$image);
            $decodedImage = base64_decode($image);

            $imageName = time().'_'.uniqid().'.jpeg';
            $path = public_path('uploads/' . $imageName);
            file_put_contents($path, $decodedImage);

            $empresa->rutaimagen = 'uploads/'.$imageName;
        }else {
            $empresa->rutaimagen = null;
        }
        
        $empresa->gps = $request->gps;
        $empresa->tipomoneda = $request->tipomoneda;
        $empresa->simbolomoneda = $request->simbolomoneda;
        $empresa->estado = 1;

        $empresa->save();

        return response()->json($empresa, 201);
    }
}
