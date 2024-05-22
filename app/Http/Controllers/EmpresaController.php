<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Empresa;
use Exception;
date_default_timezone_set('America/Lima');

class EmpresaController extends Controller
{
    public function index(Request $request) {
        try{
            $empresas = Empresa::where('estado', 1)->get();

            return response()->json(
                [
                    'data' => $empresas,
                    'status' => 200,
                    'ok' => true,
                    'message' => 'Empresas obtenidos correctamente'
                ]
            );
        }catch(Exception $ex){
            return response()->json(
                [
                    'data' => [],
                    'status' => 401,
                    'error' => 'Error al ejecutar la operación'
                ]
            );
        }
    }

    public function show($id, Request $request) {
        $empresa = Empresa::find($id);

        return response()->json($empresa, 200);
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'tipodocumentoid' => 'required',
            'nombre' => 'required',
            'numerodocumento' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $empresa = new Empresa();
        $empresa->nombrenegocio = strtoupper($request->nombrenegocio);
        $empresa->tipodocumentoid = $request->tipodocumentoid;
        $empresa->nombre = strtoupper($request->nombre);
        $empresa->numerodocumento = $request->numerodocumento;
        $empresa->email = isset($request->email)?$request->email:null;
        $empresa->direccion = isset($request->direccion)?$request->direccion:null;
        $empresa->telefono = isset($request->telefono)?$request->telefono:null;
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
        
        $empresa->gps = isset($request->gps)?$request->gps:0;
        $empresa->tipomoneda = isset($request->tipomoneda)?$request->tipomoneda:'PEN';
        $empresa->simbolomoneda = isset($request->simbolomoneda)?$request->simbolomoneda:'S/.';
        $empresa->estado = 1;

        $empresa->save();

        return response()->json([
            'data' => $empresa, 
            'status' => 201,
            'ok' => true
        ]);
    }

    public function update($id, Request $request) {
        $empresa = Empresa::find($id);

        $validator = Validator::make($request->all(), [
            'tipodocumentoid' => 'required',
            'nombre' => 'required',
            'numerodocumento' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $empresa->nombrenegocio = strtoupper($request->nombrenegocio);
        $empresa->tipodocumentoid = $request->tipodocumentoid;
        $empresa->nombre = strtoupper($request->nombre);
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

        $empresa->update();

        return response()->json([
            'data' => $empresa, 
            'status' => 201,
            'ok' => true
        ]);
    }

    public function destroy($id) {
        $empresa = Empresa::find($id);
        $empresa->estado = 0;

        $empresa->update();

        return response()->json([
            'data' => $empresa, 
            'status' => 201,
            'ok' => true
        ]);
    }
}
